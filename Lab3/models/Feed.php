<?php

namespace models;

use API\ApiResult;
use API\Application;
use Crada\Apidoc\Exception;
use \helpers;
use API\ApiInput;
use Parse\ParsePush;
use Parse\ParseInstallation;
use Parse\ParseClient;

class Feed {

    protected $configs;
    protected $user;

    public function __construct($configs = '') {
        $this->configs = $configs;
        isset($configs['auth_user']) && $this->user = $configs['auth_user'];
    }

    /**
     * @ApiDescription(section="Feed", description="This route will parse all available xml sources, if there are new feeds (within 24h)")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/parse")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function parseFeed($config) {

        $updated = STATUS_NO_CHANGES;

        $allFeeds = \ORM::for_table('feeds')->raw_query('SELECT * FROM feeds  ORDER BY scan_date ASC LIMIT 5')
        ->find_many();

        foreach ($allFeeds as $feed) {

            $feed->set('scan_date', date('Y-m-d H:i:s'))->save();

            $feeds = helpers\httpRequest($feed->url);

            $hash = sha1($feeds);

            if (ApiInput::validateFeed($feed->url) && $feed->xml_str_length != strlen($feeds)) {

                $doc = new \SimpleXmlElement($feeds, LIBXML_NOCDATA);
                
                if ($doc) {

                    foreach ($doc->channel->item as $item) {
                        $desc = ApiInput::rip_tags(helpers\set_var($item->description, $item->title), 5000, false);
                        //check if article is new
                        if (!\helpers\moreThan24h($item->pubDate)) {

                            $description = html_entity_decode($desc);
                            if (empty($description)) {
                                $description = $item->title;
                            }
                            $title = ApiInput::rip_tags($item->title, 300, false);
                            //raw query issue with idiOrm, switching to pdo
                            //insert unique urls
                            $title = html_entity_decode(helpers\set_var($title));
                            $response = \ORM::get_db()
                            ->prepare("INSERT INTO articles
                               (title,url,description,published) VALUES (:title,:url,:description,:published)
                               ON DUPLICATE KEY UPDATE
                               id = id")
                            ->execute(array(
                                'title' => $title,
                                'url' => helpers\set_var($item->link),
                                'description' => $description,
                                'published' => strftime("%Y-%m-%d %H:%M:%S", strtotime(helpers\set_var($item->pubDate)))));
                            $article_id = \ORM::get_db()->lastInsertId();

                            if ($article_id) {
                                \ORM::for_table('article_feeds')->create()
                                ->set('article_id', $article_id)
                                ->set('feed_id', $feed->id)
                                ->save();
                            }

                            //save article to assigned users
                            if ($article_id) {
                                $user_institutions = \ORM::for_table('user_institutions')
                                ->join('institutions', array('user_institutions.institution_id', '=', 'institutions.id'))
                                ->join('feeds', array('user_institutions.institution_id', '=', 'institutions.id'))
                                ->where('feeds.id', $feed->id)
                                ->group_by('user_institutions.user_id')
                                ->find_array();




                                //search for existing
                                $usersArticles = \ORM::for_table('user_articles')->table_alias('ua')
                                ->join('article_feeds', ['af.article_id', '=', 'ua.article_id'], 'af')
                                ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
                                ->where('f.id', $feed->id)
                                ->find_array();

                                if (!empty($user_institutions)) {
                                    $pdo = \ORM::get_db();
                                    $sql = 'INSERT INTO user_articles ( user_id, article_id ) VALUES ';

                                    foreach ($user_institutions as $key => $value) {

                                        $response = $this->userHaveArticle($usersArticles, $value['user_id'], $article_id);
                                        if (!$response) {
                                            $sql .= '( ' . $value['user_id'] . ', ' . $article_id . ' ), ';
                                        }
                                    }

                                    $sqlQuery = substr($sql, 0, -2);

                                    $statement = $pdo->prepare($sqlQuery);
                                    $statement->execute();
                                }

                                $updated = STATUS_UPDATED;
                            }
                        }
                    }
                } else {
                    $updated = STATUS_NO_CHANGES;
                    ApiResult::setCode(0);
                }

                $saved[] = ['feed' => $feed->url, 'status' => $updated];
            }

            //update the channel data in database including the new hash value
            $feed->set('xml_hash', $hash)
            ->set('xml_str_length', strlen($feeds))
            ->set('scan_date', date('Y-m-d H:i:s'))
            ->save();
        }

        //cleanup
        $this->deleteExpiredArticle();

        //IF have Updates
        if ($updated) {
            //$data = array("alert" => "Official Alert Actualizari!");
            $users = \ORM::for_table('user_settings')
            ->select(array('user_settings.*'))
            ->join('users', 'users.id = user_settings.user_id')
            ->where('users.push_notify', 1)
            ->where('user_settings.type', USER_ANDROID)
            ->find_many();

            if ($users)
                foreach ($users as $user) {
//                    $data = [
//                        'where' => ['userID' => (int) $user->user_id],
//                        'data' => [
//                            'alert' => 'Actualizări Official Alert.',
//                            'badge' => 'Increment',
//                            'sound' => 'default'
//                        ]
//                    ];
//                    $this->toUser($data, $config);
//                var_dump($user);
                    $this->sendPushNotifications($user->content, 'Actualizări Official Alert!');
                }
            }

            if (!empty($saved)) {
                ApiResult::setData($saved);
            } else {

                ApiResult::setMessage('No new feeds');
            }

            return;
        }

    //send push notifications
        public function sendPushNotifications($ids, $msg) {
        // Payload data you want to send to Android device(s)
            $data = array('message' => $msg);

            $ids = array($ids);

        // Insert real GCM API key from the Google APIs Console
        // https://code.google.com/apis/console/        
            $apiKey = 'AIzaSyD3feY7GrSR5tYpVfs63jFFCjCJfwCmJTY';

        // Set POST request body
            $post = array(
                'registration_ids' => $ids,
                'data' => $data,
                );

        // Set CURL request headers 
            $headers = array(
                'Authorization: key=' . $apiKey,
                'Content-Type: application/json'
                );

        // Initialize curl handle       
            $ch = curl_init();

        // Set URL to GCM push endpoint     
            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');

        // Set request method to POST       
            curl_setopt($ch, CURLOPT_POST, true);

        // Set custom request headers       
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it       
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set JSON post data
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

        //do not verify ssl
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Actually send the request    
            $result = curl_exec($ch);

        // Handle errors
            if (curl_errno($ch)) {
                echo 'GCM error: ' . curl_error($ch);
            }

        // Close curl handle
            curl_close($ch);

        // Debug GCM response       
            echo $result;
        }

    /**
     * @ApiDescription(section="Feed", description="Test Route")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/push")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function push() {

        $sent = 0;
        $emailResponse = array();
        $users = User::getEmails();

        if ($users) {

            foreach ($users as $user) {

                $feeds = '';
                $articles = User::getArticlesToEmail($user->id);

                if ($articles) {

                    foreach ($articles as $article) {

                        $feeds .= "<div style='border-top:#8F938F 1px solid; padding:15px 5px;'>
                        <a href='{$article->url}' style='text-decoration: none;'>
                            <h2 style='margin: 0;text-decoration: none; font-size:15px; color: #8F938F; font-weight: bold;'>{$article->name}</h2>
                            <div style='font-size:14px;'>
                                <span style='color: #8F938F;'>{$article->title}</span>
                                <span style='float:right; color: #8F938F; padding: 0px'>{$article->published}</span>
                            </div>
                        </a>
                    </div>";
                }

                $text = "<h3>Actualizări Official Alert.</h3>";
                $text .= $feeds;
                $text .= "<br/><br/>";
                $text .= "<p>O zi bună!</p>";

                $sent = Application::sendMail(
                    array($user->email), 'Actualizări official alert.', ($user->type == 4) ? $text : $text . '<br />Pentru dezabonare accesaţi acest <a href="http://' . $_SERVER['SERVER_NAME'] . '/email/unsubscribeconfirm?=&token=' . $user->email_token . '">link</a>'
                    );

                foreach ($articles as $article) {

                    User::emailSent($user->id, $article->id);
                }

                $emailResponse[] = array('email' => $user->email, 'status' => $sent);
            }
        }
    }
    !$emailResponse && ApiResult::setMessage('No Updates');
}

    /**
     * @ApiDescription(section="Settings", description="Sets if user wants to receive push notifications (1 for receive, 0 for not)")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/user/push")
     * @ApiParams(name="push", type="int", nullable=false, description="push")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function setPush($put) {

        $pushId = $put['push'];

        $settings = \ORM::for_table('users')->where('id', $this->user->id)->find_one();
        if ($settings) {
            $settings->set('push_notify', $pushId)->save();

            ApiResult::setMessage('Success');
        } else {

            \ORM::for_table('users')
            ->set('user_id', $this->user->id)
            ->set('push_notify', $pushId)
            ->save();
        }
    }

    /**
     * @ApiDescription(section="Feed", description="Get list of institutions")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/institutions")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getInstitutions() {

        $institutions = \ORM::for_table('institutions')->find_array();


        foreach ($institutions as &$institution) {

            $selected = \ORM::for_table('user_institutions');
            if ($this->user) {
                $selected->where('user_id', $this->user->id);
            }
            $querySelected = $selected->where('institution_id', $institution['id'])->find_one();

            $institution['selected'] = helpers\set_var($querySelected) ? 1 : 0;
        }

        ApiResult::setData($institutions);

        return $institutions;
    }


 /**
     * @ApiDescription(section="Institution", description="Update Institution")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/institution")
     * @ApiParams(name="id", type="int", nullable=false, description="Institution id")
     * @ApiParams(name="name", type="string", nullable=false, description="Name")
     * @ApiParams(name="url", type="string", nullable=false, description="Url")
     * @ApiParams(name="type", type="int", nullable=false, description="Type of institution")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function updateInstitution($put) {

        $feed = \ORM::for_table('institutions')->where('id', $put['id'])->find_one();

        if (isset($put['name'])) {
            $feed->set('name', $put['name']);
        }
        if (isset($put['url'])) {
            $feed->set('url', $put['url']);
        }
        if (isset($put['type'])) {
            $feed->set('type', $put['type']);
        }

        $result = $feed->save();

        if ($result) {
            ApiResult::setData($put);
        }
    }


    /**
     * @ApiDescription(section="User Dashboard", description="Get list of institutions")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/dashboard/article")
     * @ApiParams(name="article_id", type="int", nullable=false, description="Article Id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getArticleById($articleId) {
        $article = \ORM::for_table('articles')->table_alias('a')
        ->select_many(['id' => 'article_id'], 'title', ['url' => 'a.url'], 'description', 'published', 'name', 'a.file_path')
        ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
        ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
        ->join('institutions', ['i.id', '=', 'f.institution_id'], 'i')
        ->where('id', $articleId)
        ->find_array();
        if ($article) {
            return $article;
        }
        return false;
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Get list of institutions")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/dashboard/institutions")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getUserInstitutions($type = null) {


        if ($type)
            $institutions = \ORM::for_table('institutions')->where('type', $type)->find_array();
        else
            $institutions = \ORM::for_table('institutions')->find_array();


        foreach ($institutions as &$institution) {

            $selected = \ORM::for_table('user_institutions');
            if ($this->user) {
                $selected->where('user_id', $this->user->id);
            }
            $querySelected = $selected->where('institution_id', $institution['id'])->find_one();

            $institution['selected'] = helpers\set_var($querySelected) ? 1 : 0;
        }

        ApiResult::setData($institutions);

        return $institutions;
    }

    /**
     * @ApiDescription(section="Feed", description="Get list of institutions")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/institutions/all")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getInstitutionsAll($get) {
        $query = \ORM::for_table('institutions');
        if (isset($get['type'])) {
            $query->where('type', $get['type']);
        }
        $institutions = $query->find_array();

        foreach ($institutions as &$feed) {
            $subscribers = \ORM::for_table('user_institutions')->where('institution_id', $feed['id'])->find_array();
            $feed['subscribers'] = count($subscribers);
        }
        if ($institutions) {
            ApiResult::setData($institutions);
        }

        return $institutions;
    }

    /**
     * @ApiDescription(section="Feed", description="Get list of institutions")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/institutions")
     * @ApiParams(name="name", type="string", nullable=false, description="institution name")
     * @ApiParams(name="url", type="int", nullable=false, description="institution site")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function addInstitution($post) {



        \ORM::for_table('institutions')->create()
        ->set('name', $post['name'])
        ->set('url', $post['url'])
        ->set('type', $post['type'])
        ->save();

        $id = \ORM::get_db()->lastInsertId();
        if ($id) {

            $institution = \ORM::for_table('institutions')
            ->find_one($id)
            ->as_array();

            ApiResult::setData($institution);
        } else {
            ApiResult::setCode(0)->setMessage('Error inserting institution');
        }
    }

    /**
     * @ApiDescription(section="Feed", description="Get list of feeds")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/feeds")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getFeeds() {

        $feeds = \ORM::for_table('feeds')->select(array('id', 'url', 'scan_date', 'institution_id'))->find_array();

        if ($feeds) {
            foreach ($feeds as &$feed) {
                $feed['institution'] = \ORM::for_table('institutions')->find_one($feed['institution_id'])->as_array();
                unset($feed['institution_id']);
            }
        }

        ApiResult::setData($feeds);
    }

    /**
     * @ApiDescription(section="Backend", description="Get list of feeds")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/backend/feeds")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getBackendFeeds() {

        $feeds = \ORM::for_table('feeds')->select(array('id', 'url', 'scan_date', 'institution_id'))->find_array();

        ApiResult::setData($feeds);
    }

    /**
     * @ApiDescription(section="Feed", description="Update Category")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/feed")
     * @ApiParams(name="id", type="int", nullable=false, description="Feed id")
     * @ApiParams(name="url", type="string", nullable=false, description="Url")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function editFeed($put) {
        $feed = \ORM::for_table('feeds')->where('id', $put['id'])->find_one();

        if (isset($put['url'])) {
            $feed->set('url', $put['url']);
        }
        $result = $feed->save();

        if ($result) {
            ApiResult::setData($put);
        }
    }

    /**
     * @ApiDescription(section="Feed", description="Add feed")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/feed")
     * @ApiParams(name="url", type="string", nullable=false, description="feed url")
     * @ApiParams(name="institution", type="int", nullable=false, description="institution id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function addFeed($post) {
        $url = $post['url'];
        $institution_id = $post['institution'];
        //$category_id = $post['category'];

        $feed = \ORM::for_table('feeds')->where('url', $url)->find_one();
        if (!$feed) {
            \ORM::for_table('feeds')
            ->create()
            ->set('url', $url)
            ->set('institution_id', $institution_id)
            ->save();
        } else {
            ApiResult::setCode(0)
            ->setMessage('Institution feed already exists');
        }
    }

    /**
     * @ApiDescription(section="Feed", description="Creates new institution categories")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/category")
     * @ApiParams(name="name", type="string", nullable=false, description="category name")
     * @ApiParams(name="feeds", type="string", nullable=true, description="category feeds")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function addCategory($post, $feeds) {
        //the category name is inserted when there are no duplicates
        \ORM::get_db()
        ->prepare("INSERT INTO categories (name) VALUES (:name) ON DUPLICATE KEY UPDATE id = id")
        ->execute(array('name' => $post['name']));

        $id = \ORM::get_db()->lastInsertId();
        if ($id) {

            $category = \ORM::for_table('categories')
            ->find_one($id)
            ->as_array();

            if ($feeds) {

                foreach ($feeds as $key => $feed) {

                    \ORM::for_table('feeds_categories')
                    ->create(['category_id' => $category['id'], 'feed_id' => $feed])
                    ->save();
                }
            }

            ApiResult::setData($category);
        } else {
            ApiResult::setCode(0)
            ->setMessage('Category name already exists');
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="Gets institution categories")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/categories")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getCategories() {

        $categories = \ORM::for_table('categories')->find_array();
        if ($categories) {
            foreach ($categories as &$category) {

                $category['articles'] = \ORM::for_table('articles')
                ->select_many(['articles.*'], ['institution_name' => 'i.name','institution_type' => 'i.type'])
                ->join('article_feeds', ['af.article_id', '=', 'articles.id'], 'af')
                ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
                ->join('institutions', ['i.id', '=', 'f.institution_id'], 'i')
                ->left_outer_join('feeds_categories', ['af.feed_id', '=', 'fc.feed_id'], 'fc')
                ->where('fc.category_id', $category['id'])
                ->order_by_desc('articles.published')
                ->limit(2)
                ->find_array();
            }

            ApiResult::setData($categories);
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="Gets institution categories")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/feeds/categories")
     * @ApiParams(name="feeds", type="string", nullable=false, description="Feeds ID's")
     * @ApiParams(name="category_id", type="int", nullable=false, description="Category ID")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */


        /**
     * @ApiDescription(section="Frontend", description="Get las articles for specific categories")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/last")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
        public function getLastArticles() {

            $categories = \ORM::for_table('categories')->limit(4)->find_array();
            $types = \ORM::for_table('types')->find_array();
            $results = [];
            $articles = [];
            //if ($categories) {
                /*foreach ($types as $type) {
                    foreach ($categories as &$category) {

                        $articles = \ORM::for_table('articles')
                        ->select_many(['articles.*'], ['institution_name' => 'i.name'])
                        ->join('article_feeds', ['af.article_id', '=', 'articles.id'], 'af')
                        ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
                        ->join('institutions', ['i.id', '=', 'f.institution_id'], 'i')
                        ->left_outer_join('feeds_categories', ['af.feed_id', '=', 'fc.feed_id'], 'fc')
                        ->where('fc.category_id', $category['id'])
                        ->where('i.id', $type['id'])
                        ->order_by_desc('articles.published')
                        ->limit(1)
                        ->find_array();
                    }
                    $results[] = $categories;
                }*/
                foreach( $types as $type ) {
                    $articles = null;
                    foreach ($categories as $category) {
                       // $article = null;
                        $article = \ORM::for_table('articles')
                        ->select_many(['articles.*'], ['institution_name' => 'i.name'])
                        ->join('article_feeds', ['af.article_id', '=', 'articles.id'], 'af')
                        ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
                        ->join('institutions', ['i.id', '=', 'f.institution_id'], 'i')
                        ->join('feeds_categories',['fdc.feed_id', '=', 'f.id'],'fdc')
                        ->where('fdc.category_id',$category['id'])
                        ->where('i.type', $type['id'])
                        ->order_by_desc('articles.published')
                        ->limit(1)
                        ->find_array();
                        $articles[] = $article;
                    }
                    $results[] = $articles;
                }
                //var_dump($results);
                ApiResult::setData($results);
            //}
        }

    /**
     * @ApiDescription(section="Frontend", description="Gets institution categories")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/feeds/categories")
     * @ApiParams(name="feeds", type="string", nullable=false, description="Feeds ID's")
     * @ApiParams(name="category_id", type="int", nullable=false, description="Category ID")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */


    public function editFeedsCategories($put) {

        foreach ($put['feeds'] as $key => $feed) {
            \ORM::for_table('feeds_categories')
            ->create(['category_id' => $put['category_id'], 'feed_id' => $feed])
            ->save();
        }

//        $feed = \ORM::for_table('feeds')->where('id', $put['id'])->find_one();
//        $feed = \ORM::for_table('feeds')->where('id', $put['id'])->find_one();
    }

    /**
     * @ApiDescription(section="Frontend", description="Gets institution categories")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/feeds/categories")
     * @ApiParams(name="feeds", type="string", nullable=false, description="Feeds ID's")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function deleteFeedsCategories($put) {

        foreach ($put['feeds'] as $feedId) {
            $feed = \ORM::for_table('feeds_categories')
            ->where('feed_id', $feedId)
            ->find_one();
            $feed->delete();
        }

//        $feed = \ORM::for_table('feeds')->where('id', $put['id'])->find_one();
//        $feed = \ORM::for_table('feeds')->where('id', $put['id'])->find_one();
    }

    /**
     * @ApiDescription(section="Feed", description="Update Category")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/category")
     * @ApiParams(name="id", type="int", nullable=false, description="Edit")
     * @ApiParams(name="name", type="string", nullable=false, description="Edit")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function editCategory($put) {

        $byName = \ORM::for_table('categories')->where('name', $put['name'])->find_one();

        if ($byName) {
            ApiResult::setCode(0)
            ->setMessage('This Category Exist');
        } else {
            $category = \ORM::for_table('categories')->where('id', $put['id'])->find_one();

            if ($category) {
                $result = $category->set('name', $put['name'])->save();
                if ($result) {
                    ApiResult::setData($put);
                } else {
                    ApiResult::setCode(0)
                    ->setMessage('Data not inserted');
                }
            } else {
                ApiResult::setCode(0)
                ->setMessage('Invalid id');
            }
        }
    }

    public function getTestimonials() {
        $testimonials = \ORM::for_table('testimonials')->order_by_asc('position')->find_array();
        if ($testimonials) {
            ApiResult::setData($testimonials);
        }
        return $testimonials;
    }

    public function updateTestimonials($data) {
        //prevent updating id
        if (isset($data['position'])) {
            $data['position'] = (int) $data['position'];
        }
        $testimonial = \ORM::for_table('testimonials')->find_one($data['id']);
        $testimonial->set($data);
        $testimonial->save();

        ApiResult::setData(['id' => $data['id']]);
    }

    public function insertTestimonials($data) {
        $testimonial = \ORM::for_table('testimonials')->create();
        foreach ($data as $key => $item) {
            $testimonial->{$key} = $item;
        }
        $testimonial->save();

        ApiResult::setData(['id' => $testimonial->id]);
    }

    public function deleteTestimonials($data) {
        $testimonial = \ORM::for_table('testimonials')->find_one($data['id']);
        $testimonial->delete();
    }

    /**
     * About Us CRUD Backend
     * 
     * 
     */
    public function getAbout() {
        $description = \ORM::for_table('about_us')->find_array();
        if ($description)
            ApiResult::setData($description);
        return $description;
    }

    public function updateAbout($data) {

        if (isset($data['$$hashKey']))
            unset($data['$$hashKey']);

        $description = \ORM::for_table('about_us')->find_one($data['id']);
        $description->set($data);
        $description->save();
        ApiResult::setData(['id' => $data['id']]);
    }

    /**
     * deletes expired articles (more than 24h) but leaves at least one article for each feed
     */
    public function deleteExpiredArticle() {

        $articles = \ORM::for_table('articles')->table_alias('a')
        ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
        ->select_many(['id' => 'a.id', 'file_path' => 'a.file_path', 'published' => 'a.published', 'id_feed' => 'af.feed_id'])
        ->order_by_asc('published')
        ->find_array();


        $articlesForDeleteObject = [];
        foreach ($articles as $article) {
            if (\helpers\moreThan24h($article['published'])) {
                $articlesForDeleteObject[$article['id_feed']][] = $article;
            }
        }


        foreach ($articlesForDeleteObject as &$feed) {
            unset($feed[count($feed) - 1]);
        }


        $articlesForDelete = [];
        foreach ($articlesForDeleteObject as $feed) {
            foreach ($feed as $article) {
                $articlesForDelete[] = $article['id'];
            }
        }

        if ($articlesForDelete) {
            \ORM::for_table('articles')->where_id_in($articlesForDelete)->delete_many();
        }
    }

    public function toUser($data, $config = null) {

        $X = curl_init('https://parse.com/1/push');
        $D = json_encode($data);
        curl_setopt_array($X, [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
            'X-Parse-Application-Id: ' . $config['app_id'],
            'X-Parse-REST-API-Key: ' . $config['rest_key'],
            'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => $D
            ]);
        curl_exec($X);
    }

    public static function userHaveArticle($usersArticles, $userId, $articleId) {
        foreach ($usersArticles as $key => $value) {
            if ($value['user_id'] == $userId && $value['article_id'] == $articleId) {
                return true;
            }
        }
        return false;
    }

    public function getTypesAll() {
        $types = \ORM::for_table('types')->find_array();

        if ($types) {
            ApiResult::setData($types);
        }

        return $types;
    }

    /*
     *  BLOG CRUD & Tools
     */

    public static function getDashboardBlog() {
        $list = \ORM::for_table('blog_posts')
        ->order_by_desc('id')
        ->find_array();
        ApiResult::setData($list);
        return $list;
    }

    public static function updateBlog($data) {

        $blog = \ORM::for_table('blog_posts')->find_one($data['id']);
        $data['slug'] = self::slugify($blog->title);
        $blog->set($data);
        $blog->save();
        ApiResult::setData(['id' => $data['id']]);
    }

    public static function insertBlog($data) {
        $blog = \ORM::for_table('blog_posts')->create();
        $blog->slug = self::slugify($blog->title);
        $blog->created_at = date('Y-m-d H:i:s');
        foreach ($data as $key => $item) {
            $blog->{$key} = $item;
        }
        $blog->save();

        ApiResult::setData(['id' => $blog->id]);
    }

    public static function deleteBlog($delete) {
        $blog = \ORM::for_table('blog_posts')->find_one($delete['id']);
        $blog->delete();
    }

    public static function getBlogPaginate($page) {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if ($page == 1) {
            $offset = 0;
        }
        $blog = \ORM::for_table('blog_posts')->raw_query('SELECT * FROM `blog_posts` LIMIT ' . $limit . ' OFFSET ' . $offset)->find_array();

        foreach ($blog as $key => $post) {
            //generate date
            $month = date('M', strtotime($post['created_at']));
            $date = date('d', strtotime($post['created_at']));
            $year = date('Y', strtotime($post['created_at']));

            $blog[$key]['date'] = [
            'month' => $month,
            'date' => $date,
            'year' => $year
            ];
        }

        return [
        'posts' => $blog,
        'count' => \ORM::for_table('blog_posts')->count()
        ];
    }

    public static function getPostBySlug($slug) {
        $post = \ORM::for_table('blog_posts')
        ->where('slug', $slug)->limit(1)
        ->find_array();

        if (isset($post[0])) {
            $post = $post[0];

            //generate date
            $month = date('m', strtotime($post['created_at']));
            $date = date('d', strtotime($post['created_at']));
            $year = date('Y', strtotime($post['created_at']));

            $post['date'] = [
            'month' => self::$months[$month],
            'date' => $date,
            'year' => $year
            ];


            $post['tags'] = explode(',', $post['tags']);
        }



        return $post;
    }

    private static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static $months = [
    '01' => 'Ianuarie',
    '02' => 'Februarie',
    '03' => 'Martie',
    '04' => 'Aprilie',
    '05' => 'Mai',
    '06' => 'Iunie',
    '07' => 'Iulie',
    '08' => 'August',
    '09' => 'Septembrie',
    '10' => 'Octombrie',
    '11' => 'Noiembrie',
    '12' => 'Decembrie'
    ];

    /*
     * Get comments for post
     */
    public function getComments($get) {
        $comments = \ORM::for_table('comments')->where('post', $get['post'])->find_array();
        
        foreach($comments as $key=>$comment){

         $user =  \ORM::for_table('users')->select('first_name')->select('surname')->where('id',$comment['user'])->find_array();
         if(isset($user[0]))
          $user = $user[0];


      $comments[$key]['user_data'] = $user;
  }

  ApiResult::setData($comments);
  return $comments;
}

public function addComment($post) {
 $comment =  \ORM::for_table('comments')->create();
 $comment->user = $this->user->id;
 $comment->comment = $post['comment'];
 $comment->post = $post['post'];
 $comment->created_at = date('Y-m-d H:i:s');
 $comment->save();
}

public function checkCaptcha($post){

    $response = $post['response'];

        // Set CURL request headers 
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded'
        );

    $post = [
    "secret"=> "6LeiDCoTAAAAACpVfM3LrhNYA2vQ78uH2nDrh1UD", 
    "response"=> $response
    ];


        // Initialize curl handle       
    $ch = curl_init();

        // Set URL to GCM push endpoint     
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');

        // Set request method to POST       
    curl_setopt($ch, CURLOPT_POST, true);

        // Set custom request headers       
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get the response back as string instead of printing it       
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set JSON post data
    curl_setopt($ch, CURLOPT_POSTFIELDS, "response=".$post['response'].'&secret='.$post['secret']);

        //do not verify ssl
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Actually send the request    
    $result = curl_exec($ch);

        // Close curl handle
    curl_close($ch);


    ApiResult::setData(json_decode($result));        

}

public function getAllComments() {
        //get all comments list with author data
    $blog = \ORM::for_table('blog_posts')
    ->raw_query('SELECT t1.*,CONCAT(t2.first_name," ", t2.surname) as name FROM '
        . '`comments` as t1 '
        . 'JOIN `users` as t2 '
        . 'ON t1.user = t2.id '
        . 'ORDER BY t1.id DESC')
    ->find_array();

    ApiResult::setData($blog);
    return $blog;
}

public function removeComment($comment) {
    $blog = \ORM::for_table('comments')->find_one($comment['id']);
    $blog->delete();
    ApiResult::setData($comment['id']);
    return true;
}

}
