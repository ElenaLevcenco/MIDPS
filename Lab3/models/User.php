<?php

namespace models;

use API\ApiInput;
use API\ApiResult;
use helpers;

class User {

    protected $configs;
    protected $user;

    public function __construct($configs = '') {
        $this->configs = $configs;
        isset($configs['auth_user']) && $this->user = $configs['auth_user'];
    }

    /**
     * @ApiDescription(section="User", description="register user/email")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/login")
     * @ApiParams(name="token", type="string", nullable=false, description="Device token")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function register($post) {
        $user = \ORM::for_table('user_settings')
                        ->join('users', array('users.id', '=', 'user_settings.user_id'))
                        ->where('content', $post['token'])->find_array();

        if (!$user) {
            //the user is inserted when there are no duplicates
            \ORM::for_table('users')->create()
                    ->set('push_notify', 1)
                    ->save();

            $id = \ORM::get_db()->lastInsertId();

            \ORM::for_table('user_settings')->create()
                    ->set('user_id', $id)
                    ->set('content', $post['token'])
                    ->set('type', 1)
                    ->save();

            $user = \ORM::for_table('user_settings')
                            ->join('users', array('users.id', '=', 'user_settings.user_id'))
                            ->where('users.id', $id)->find_array();

            $institutions = \ORM::for_table('institutions')->find_many();

            foreach ($institutions as $institution) {

                \ORM::for_table('user_institutions')
                        ->create(array('user_id' => $id, 'institution_id' => $institution->id))
                        ->save();

                $feeds = \ORM::for_table('feeds')->where('institution_id', $institution->id)->find_many();
                $i = 0;

                foreach ($feeds as $feed) {

                    $articles = \ORM::for_table('articles')->select('articles.id')
                                    ->join('article_feeds', ['af.article_id', '=', 'articles.id'], 'af')
                                    ->where('af.feed_id', $feed->id)->find_array();

                    if ($articles) {

                        foreach ($articles as &$article) {
                            $article['user_id'] = $id;
                            $article['article_id'] = $article['id'];
                            unset($article['id']);
                        }

                        $datafields = array('user_id', 'article_id');

                        $pdo = \ORM::get_db();

                        $insert_values = array();
                        $question_marks = [];
                        foreach ($articles as $d) {
                            $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
                            $insert_values = array_merge($insert_values, array_values($d));
                        }

                        $sql = "INSERT INTO user_articles (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

                        $stmt = $pdo->prepare($sql);
                        //var_dump($stmt);
                        try {
                            $stmt->execute($insert_values);
                        } catch (\PDOException $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }
        }

        unset($user['id']);
        ApiResult::setData($user);
    }

    /**
     * @ApiDescription(section="User Dashboard", description="register user/email")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/dashboard/login")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiParams(name="password", type="string", nullable=false, description="Password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function login($post) {

        $user = \ORM::for_table('user_settings')
                ->join('users', ['users.id', '=', 'user_settings.user_id'])
                ->where('users.email', $post['email'])
                ->where('type', 4)
                ->where('confirmed', 1)
                ->find_array();

        if (!empty($user)) {

            if ($user[0]['password'] == sha1($post['email'] . $post['password'])) {

                unset($user[0]['password']);
                unset($user[0]['email']);
                unset($user[0]['id']);

                ApiResult::setData($user[0]);
            } else {
                ApiResult::setCode(0);
                ApiResult::setMessage('Login Failed');
            }
        } else {
            ApiResult::setCode(0);
            ApiResult::setMessage('Login Failed');
        }
    }

    /**
     * @ApiDescription(section="User Dashboard", description="register user/email")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/dashboard/recovery/confirm")
     * @ApiParams(name="email_token", type="string", nullable=false, description="Email Token")
     * @ApiParams(name="password", type="string", nullable=false, description="Password")
     * @ApiParams(name="verify_password", type="string", nullable=false, description="Verify Password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function changePassword($post) {
        $user = \ORM::for_table('users')->where('email_token', $post['email_token'])->find_one();
        if ($user) {
            $user->set('password', sha1($user->email . $post['password']));
            $saved = $user->save();
            if ($saved) {
                $user = \ORM::for_table('users')->table_alias('u')
                                ->join('user_settings', ['us.user_id', '=', 'u.id'], 'us')
                                ->where('email', $user->email)->find_array();
                unset($user[0]['password']);
                return $user;
            }
        }
        return false;
    }

    /**
     * @ApiDescription(section="User Dashboard", description="register user/email")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/dashboard/recovery")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function passwordRecover($email) {
        $user = $this->getUserByEmail($email);

        if ($user) {
            $address = 'http://' . $_SERVER['SERVER_NAME'] . '/email/recoveryconfirm?=&token=' . $user->email_token;

            $sent = \API\Application::sendMail(
                            [$email], 'Official Alert', "Va rugăm accesaţi acest <a href='$address'>link</a> pentru a restabili parola."
            );
            if ($sent) {
                return true;
            }
        }
        return false;
    }

    /**
     * @ApiDescription(section="User Dashboard", description="register user/email")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/dashboard/register")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiParams(name="first_name", type="string", nullable=false, description="Nume")
     * @ApiParams(name="surname", type="string", nullable=false, description="Familie")
     * @ApiParams(name="password", type="string", nullable=false, description="First Pa")
     * @ApiParams(name="verify-password", type="string", nullable=false, description="Password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function clientRegister($put) {

        $user = \ORM::for_table('users')
                ->where('email', $put['email'])
                ->find_one();
        if ($user) {
            $password = sha1($put['email'] . $put['password']);

            $user->set('password', $password);
            $user->set('first_name', $put['first_name']);
            $user->set('surname', $put['surname']);
            $user->set('email_notify', 1);
            $user->save();
            $userSettings = \ORM::for_table('user_settings')
                    ->where('user_id', $user->id)
                    ->find_one();

            $userSettings->set('type', 4);
            $userSettings->set('content', helpers\tokenGenerator());
            $userSettings->set('confirmed', 0);
            $userSettings->save();

            $address = 'http://' . $_SERVER['SERVER_NAME'] . '/email/registerconfirm?=&token=' . $user->email_token;

            $sent = \API\Application::sendMail(
                            [$user->email], 'Official Alert', "Va rugăm accesaţi acest <a href='$address'>link</a> pentru a confirma înregistrarea."
            );
        } else {
            $token = $this->createUser($put['email'], $put['password'], $put['first_name'], $put['surname']);

            $address = 'http://' . $_SERVER['SERVER_NAME'] . '/email/registerconfirm?=&token=' . $token;
            //ApiResult::setMessage('Please confirm email');
            $sent = \API\Application::sendMail(
                            [$put['email']], 'Official Alert', "Va rugăm accesaţi acest <a href='$address'>link</a> pentru a confirma înregistrarea."
            );
        }

        if ($sent) {
            ApiResult::setMessage('Vă rugăm confirmați email-ul');
        } else {
            ApiResult::setCode(0);
            ApiResult::setMessage('Register Failed');
        }
    }

    /**
     * @ApiDescription(section="User", description="Get list of articles for current user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/user/articles")
     * @ApiParams(name="start", type="int", nullable=false, description="Start article")
     * @ApiParams(name="limit", type="int", nullable=false, description="Limit articles")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getArticles($get) {

//        $userArticles = $this->getUserArticles($this->user->id ,$articles = array(), $get['limit'], $get['start']);

        if (!isset($get['type']))
            $get['type'] = null;

        $userArticles = $this->getUserArticles($this->user->id, $articles = array(), null, null, $get['type']);
        ApiResult::setData($userArticles);
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Get list of articles for current user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/dashboard/articles")
     * @ApiParams(name="start", type="int", nullable=false, description="Start article")
     * @ApiParams(name="limit", type="int", nullable=false, description="Limit articles")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getDashboardArticles($get) {
        $limit = null;
        $start = null;
        if (array_key_exists('limit', $get)) {
            $limit = $get['limit'];
        }
        if (array_key_exists('start', $get)) {
            $start = $get['start'];
        }
        $userArticles = $this->getUserDashboardArticles($this->user->id, $limit, $start);

        if ($userArticles) {
            ApiResult::setData($userArticles);
        } else {
            ApiResult::setMessage('Nu sunt articole');
        }
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Get list of articles for current user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/dashboard/profile")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getDashboardProfile() {

        $userProfile = \ORM::for_table('users')
                        ->select_many('email', 'first_name', 'surname', 'address', 'phone', 'email_notify', 'more')
                        ->join('user_settings', ['us.user_id', '=', 'users.id'], 'us')
                        ->where('users.id', $this->user->id)->find_array();
        if ($userProfile) {
            return $userProfile;
        } else {
            return false;
        }
    }

    public function checkUserExist($email) {
        $user = \ORM::for_table('user_settings')
                ->join('users', 'users.id = user_settings.user_id')
                ->where('user_settings.type', 4)
                ->where('users.email', $email)
                ->find_one();
        if (!$user) {
            return false;
        }
        return true;
    }

    /**
     * @ApiDescription(section="User", description="Sets article visited")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/user/articles")
     * @ApiParams(name="articles", type="array", nullable=false, description="article ids")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function putArticles($put) {
        if (array_key_exists('articles', $put)) {
            $userArticles = $this->getUserArticles($this->user->id, $put['articles']);
        } else {
            $userArticles = $this->getUserArticles($this->user->id);
        }

        if ($userArticles) {

            foreach ($userArticles as $article) {
                User::articleVisited($this->user->id, $article['id']);
            }
        }

        ApiResult::setData($userArticles);
    }

    /**
     * @ApiDescription(section="Settings", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/user/feed")
     * @ApiParams(name="institution", type="int", nullable=false, description="institution id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function updateFeed($post) {

        $institutions = $post['institution'];

        \ORM::for_table('user_institutions')
                ->where('user_id', $this->user->id)
                ->delete_many();

        foreach ($institutions as $institution) {
            \ORM::for_table('user_institutions')
                    ->create()
                    ->set('user_id', $this->user->id)
                    ->set('institution_id', $institution)
                    ->save();
        }

        $feeds = \ORM::for_table('institutions')
                ->where_id_in($institutions)
                ->find_array();

        ApiResult::setMessage('Feeds Updated')
                ->setData($feeds);
    }

    /**
     * @ApiDescription(section="Settings", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/user/institution")
     * @ApiParams(name="status", type="int", nullable=false, description="status of institution 1 active 0 delete")
     * @ApiParams(name="institution", type="array", nullable=false, description="array of institutions id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function updateUserInstitution($status, $institution_id) {

        $institution = \ORM::for_table('institutions')->where('id', $institution_id)->find_one();

        if ($institution) {

            if ($status) {
                $user_institution = \ORM::for_table('user_institutions')
                        ->where('institution_id', $institution_id)
                        ->where('user_id', $this->user->id)
                        ->find_one();
                if (!$user_institution) {
                    \ORM::for_table('user_institutions')->create()
                            ->set('user_id', $this->user->id)
                            ->set('institution_id', $institution_id)
                            ->save();
                    $feeds = \ORM::for_table('feeds')->where('institution_id', $institution->id)->find_many();

                    foreach ($feeds as $feed) {
                        $articles = \ORM::for_table('articles')
                                        ->join('article_feeds', ['af.article_id', '=', 'articles.id'], 'af')
                                        ->where('af.feed_id', $feed->id)->find_many();

                        if ($articles) {
                            foreach ($articles as $article) {

                                \ORM::for_table('user_articles')->create()
                                        ->set('user_id', $this->user->id)
                                        ->set('article_id', $article->article_id)
                                        ->save();
                            }
                        }
                    }
                }
            } else {

                $pdo = \ORM::get_db();
                $query = $pdo->prepare("DELETE ua FROM user_articles ua
	                                    JOIN article_feeds ON article_feeds.article_id = ua.article_id
                                        JOIN feeds ON feeds.id = article_feeds.feed_id
                                        JOIN institutions ON institutions.id = feeds.institution_id

                                        WHERE ua.user_id = :user_id AND feeds.institution_id = :institution_id");
                $query->execute(array('user_id' => $this->user->id, 'institution_id' => $institution_id));

                $user_institution = \ORM::for_table('user_institutions')->where('user_id', $this->user->id)->where('institution_id', $institution_id)->find_one();
                if ($user_institution) {
                    $user_institution->delete();
                }

                ApiResult::setMessage('Instituțiile au fost reânnoite');
            }
        } else {
            ApiResult::setMessage('Invalid Institution id');
        }
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/dashboard/institutions")
     * @ApiParams(name="institutions", type="array", nullable=false, description="array of institutions id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function updateUserInstitutions($institutionsId) {

        \ORM::for_table('user_institutions')->where('user_id', $this->user->id)->delete_many();
        \ORM::for_table('user_articles')->where('user_id', $this->user->id)->delete_many();

        $institutions = [];
        $responses = [];

        foreach ($institutionsId as $key => $institution) {

            $institutions[$key]['user_id'] = $this->user->id;
            $institutions[$key]['institution_id'] = $institution;
            //unset($institution['id']);
        }

        $dataFields = ['user_id', 'institution_id'];

        $pdo = \ORM::get_db();

        $insert_values = [];
        $question_marks = [];
        foreach ($institutions as $d) {
            $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
            $insert_values = array_merge($insert_values, array_values($d));
        }

        $sql = "INSERT INTO user_institutions (" . implode(",", $dataFields) . ") VALUES " . implode(',', $question_marks);

        $stmt = $pdo->prepare($sql);

        try {
            $responses[] = $stmt->execute($insert_values);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

        foreach ($institutionsId as $institutionId) {

            $feeds = \ORM::for_table('feeds')->where('institution_id', $institutionId)->find_many();

            foreach ($feeds as $feed) {
                $articles = \ORM::for_table('articles')->table_alias('a')->select('a.id')
                        ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
                        ->where('af.feed_id', $feed->id)
                        ->group_by('a.id')
                        ->find_array();

                if ($articles) {

                    foreach ($articles as &$article) {
                        $article['user_id'] = $this->user->id;
                        $article['article_id'] = $article['id'];
                        unset($article['id']);
                    }

                    $dataFields = ['user_id', 'article_id'];

                    $pdo = \ORM::get_db();

                    $insert_values = [];
                    $question_marks = [];
                    foreach ($articles as $d) {
                        $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
                        $insert_values = array_merge($insert_values, array_values($d));
                    }

                    $sql = "INSERT INTO user_articles (" . implode(",", $dataFields) . ") VALUES " . implode(',', $question_marks);
                    $stmt = $pdo->prepare($sql);

                    try {
                        $responses[] = $stmt->execute($insert_values);
                    } catch (\PDOException $e) {
                        echo $e->getMessage();
                    }
                }
            }
        }

        if (!in_array(false, $responses)) {
            ApiResult::setMessage('Modificare cu success.');
        }
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/dashboard/profile")
     * @ApiParams(name="first_name", type="string", nullable=false, description="First Name")
     * @ApiParams(name="surname", type="string", nullable=false, description="Surname")
     * @ApiParams(name="address", type="string", nullable=false, description="Address")
     * @ApiParams(name="phone", type="string", nullable=false, description="Phone")
     * @ApiParams(name="email_notify", type="int", nullable=false, description="Email Notify")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function updateUserProfile($put) {
        $user = \ORM::for_table('users')->where('id', $this->user->id)->find_one();

        if (!empty($put['first_name']))
            $user->set('first_name', $put['first_name']);
        if (!empty($put['surname']))
            $user->set('surname', $put['surname']);
        if (!empty($put['address']))
            $user->set('address', $put['address']);
        if (!empty($put['phone']))
            $user->set('phone', $put['phone']);
        if (isset($put['email_notify']))
            $user->set('email_notify', (int) $put['email_notify']);

        return $user->save();
    }

    /**
     * @ApiDescription(section="User Dashboard", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/dashboard/password")
     * @ApiParams(name="password", type="string", nullable=false, description="Password")
     * @ApiParams(name="new_password", type="string", nullable=false, description="New Password")
     * @ApiParams(name="verify_password", type="string", nullable=false, description="Verify Password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function updateUserPassword($put) {
        $saved = false;
        $user = \ORM::for_table('users')->where('id', $this->user->id)->find_one();
        if (empty($put['password']) || empty($put['new_password']) || empty($put['verify_password'])) {
            ApiResult::setMessage('Vă rugăm completați toate cîmpurile');
            ApiResult::setCode(0);
        }
        if (!empty($put['new_password']) && !empty($put['verify_password']) && !empty($put['password'])) {
            if ($put['new_password'] == $put['verify_password']) {
                if ($user->password == sha1($user->email . $put['password'])) {
                    $user->set('password', sha1($user->email . $put['new_password']));
                    $saved = $user->save();
                } else {
                    ApiResult::setMessage('Parola este greșită');
                    ApiResult::setCode(0);
                }
            } else {
                ApiResult::setCode(0);
                ApiResult::setMessage('Parola nu coincide');
            }
        } elseif (isset($put['new_password']) && isset($put['verify_password']) && !isset($put['password'])) {
            ApiResult::setCode(0);
            ApiResult::setMessage('Introduceți parola curentă');
        }
        if ($saved) {
            ApiResult::setCode(1);
            ApiResult::setMessage('Parola a fost schimbată');
        }
    }

    //---------------------------------static functions--------------------------------------------------------


    /*     * get users registered from devices
     * @return array|\IdiormResultSet
     */

    public static function getDevices() {
        return \ORM::for_table('users')
                        ->where_not_null('token')
                        ->where('type', USER_ANDROID)
                        ->find_many();
    }

    /*     * get users registered with emails
     * @return array|\IdiormResultSet
     */

    public static function getEmails() {
        $user = \ORM::for_table('user_settings')->table_alias('us')
                ->join('users', array('users.id', '=', 'us.user_id'))
                ->where_in('us.type', [2, 4])
                ->where('users.email_notify', 1)
                ->find_many();

        return $user;
    }

    /*     * gets user articles
     * @param $user_id
     * @return array
     */

    public static function getUserArticles($user_id, $articles = array(), $limit = null, $start = null, $type = null) {

        $datas = \ORM::for_table('articles')
                ->select(array('articles.*', 'user_articles.visited'))
                ->select('institutions.id', 'institution_id')
                ->join('user_articles', array('articles.id', '=', 'user_articles.article_id'))
                ->join('article_feeds', array('article_feeds.article_id', '=', 'user_articles.article_id'))
                ->join('feeds', array('feeds.id', '=', 'article_feeds.feed_id'))
                ->join('institutions', array('institutions.id', '=', 'feeds.institution_id'))
                ->where('user_articles.user_id', $user_id)
                ->order_by_desc('published')
                ->group_by('user_articles.article_id');

        !empty($articles) && $datas->where_in('articles.id', $articles);
        ($limit) && $datas->limit($limit);
        ($start) && $datas->offset($start);
        if ($type) {
            $datas->where('institutions.type', $type);
        }
        $feeds = $datas->find_array();

        foreach ($feeds as &$feed) {
            if ($feed['file_path'] != NULL) {
                $feed['file_path'] = helpers\getBaseUrl() . $feed['file_path'];
            }
        }

        foreach ($feeds as &$feed) {
            $feed['institution'] = \ORM::for_table('institutions')->find_one($feed['institution_id'])->as_array();
            unset($feed['institution_id']);
        }


        return $feeds;
    }

    public static function getUserDashboardArticles($user_id, $limit = null, $start = null) {
        $data = \ORM::for_table('user_articles')->table_alias('ua')
                ->select(array('i.id','ua.user_id','ua.article_id','ua.visited','ua.email_sent','a.title','a.url','a.description','a.published','a.file_path','af.feed_id','f.institution_id','f.xml_hash','f.scan_date','f.xml_str_length','i.name','i.type'))
                ->join('articles', ['a.id', '=', 'ua.article_id'], 'a')
                ->join('article_feeds', ['a.id', '=', 'af.article_id'], 'af')
                ->join('feeds', ['f.id', '=', 'af.feed_id'], 'f')
                ->join('institutions', ['i.id', '=', 'f.institution_id'], 'i')
                ->where('ua.user_id', $user_id)
                ->order_by_desc('published')
                ->group_by('ua.article_id');

        ($limit) && $data->limit($limit);
        ($start) && $data->offset($start);
        $articles = $data->find_array();

        foreach ($articles as &$article) {
            $article['description'] = ApiInput::rip_tags($article['description'], 200, false);

            if ($article['file_path'] != NULL) {
                $article['file_path'] = helpers\getBaseUrl() . $article['file_path'];
            }
        }

        return $articles;

    }

    /*     * gets user articles
     * @param $userId
     * @return array
     */

    public static function getArticlesToEmail($userId) {
        $articles = \ORM::for_table('articles')->table_alias('a')
                ->select_many('a.*', 'ua.email_sent', 'institutions.id', 'institution_id'
                        , 'institutions.name', 'name', 'a.id')
                ->join('user_articles', ['a.id', '=', 'ua.article_id'], 'ua')
                ->join('article_feeds', ['af.article_id', '=', 'ua.article_id'], 'af')
                ->join('feeds', ['feeds.id', '=', 'af.feed_id'])
                ->join('institutions', ['institutions.id', '=', 'feeds.institution_id'])
                ->where('ua.user_id', $userId)
                ->where('ua.email_sent', 0)
                ->group_by('ua.article_id')
                ->find_many();

        return $articles;
    }

    /*     * Update article send trough email
     * @param $user
     * @param $article
     * @return bool
     */

    public static function emailSent($userId, $articleId) {

        $userArticle = \ORM::for_table('user_articles')
                ->where('user_id', $userId)
                ->where('article_id', $articleId)
                ->find_one();

        if ($userArticle) {

            $userArticle->set('email_sent', 1)
                    ->save();

            return true;
        } else {
            return false;
        }
    }

    public static function getUserByEmail($email) {
        $user = \ORM::for_table('users')->where('email', $email)->find_one();
        if ($user) {
            return $user;
        }

        return false;
    }

    public static function getUserByEmailToken($token) {
        $user = \ORM::for_table('users')->where('email_token', $token)->find_one();
        if ($user) {
            return $user;
        }

        return false;
    }

    /*     * Update article set visited
     * @param $user
     * @param $article
     * @return bool
     */

    public static function articleVisited($user, $article) {

        $user_articles = \ORM::for_table('user_articles')
                ->where('user_id', $user)
                ->where('article_id', $article)
                ->find_one();

        if ($user_articles) {

            $user_articles->set('visited', 1)->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="User frontend subscribe")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/subscribe")
     * @ApiParams(name="email", type="string", nullable=false, description="User email")
     * @ApiParams(name="institutions", type="array", nullable=false, description="array of institutions id")
     * @ApiParams(name="more", type="int", nullable=true, description="More True or false")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function subscribe($email, $institutions, $more) {
        $user = \ORM::for_table('user_settings')
                ->join('users', 'users.id = user_settings.user_id')
                ->where('users.email', $email)
                ->find_one();

        $userId = '1';
        if (!$user) {

            $token = helpers\randomString();

            $address = 'http://' . $_SERVER['SERVER_NAME'] . '/email/subscribeconfirm?=&token=' . $token;

            $sent = \API\Application::sendMail(
                            array($email), 'Official Alert', "Va rugăm accesaţi acest <a href='$address'>link</a> pentru a confirma abonarea."
            );

            //the user is inserted when there are no duplicates
            $pdo = \ORM::get_db();
            $pdo->prepare("INSERT INTO users (push_notify, more, email_token, email, email_notify) VALUES (:push_notify,$more, :token, :email, :email_notify) ON DUPLICATE KEY UPDATE id = id")
                    ->execute(array('push_notify' => 1, 'token' => $token, 'email' => $email, 'email_notify' => 0));
            $userId = $pdo->lastInsertId();

            \ORM::for_table('user_settings')->create()
                    ->set('user_id', $userId)
                    ->set('type', USER_EMAIL)
                    ->set('active', 0)
                    ->save();

            $user = \ORM::for_table('users')->where('email', $email)->find_one();
            //Save User institutions in temp table

            foreach ($institutions as $institution) {
                \ORM::for_table('temp_user_institutions')
                        ->create(array('user_id' => $user->id, 'institution_id' => $institution))
                        ->save();
            }
            //var_dump('Success mother fucker');die;
            ApiResult::setMessage('Va rugăm să confirmaţi abonarea');
        } elseif ($user || $user->active == 0) {
            ApiResult::setCode(0);
            ApiResult::setMessage('Sunteţi deja abonat!');
        } else {

            //If user == 0 user Exist
            if ($userId == '0') {
                $user = \ORM::for_table('users')->where('email', $email)->find_one();
            }

            \ORM::for_table('user_institutions')->where('user_id', $user->id)->delete_many();

            foreach ($institutions as $institution) {

                \ORM::for_table('user_institutions')
                        ->create(array('user_id' => $user->id, 'institution_id' => $institution))
                        ->save();
            }

            ApiResult::setMessage('Instituțiile au fost reînnoite');
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="Confirm Unsubscribe from email")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/email/subscribeconfirm")
     * @ApiParams(name="token", type="int", nullable=false, description="Token Code for Unsubscribe")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function subscribeConfirm($token) {

        $user = \ORM::for_table('users')->where('email_token', $token)->find_one();

        if ($user) {
            $userSettings = \ORM::for_table('user_settings')->where('user_id', $user->id)->find_one();
            $userSettings->set('active', 1)->save();

            $pdo = \ORM::get_db();

            $query = $pdo->prepare('INSERT INTO user_institutions SELECT * FROM temp_user_institutions WHERE user_id = :user_id; DELETE FROM temp_user_institutions WHERE user_id = :user_id');
            $query->execute(array('user_id' => $user->id));

            $user_institutions = \ORM::for_table('user_institutions')->where('user_id', $user->id)->find_many();

            foreach ($user_institutions as $user_institution) {
                $feeds = \ORM::for_table('feeds')->where('institution_id', $user_institution->institution_id)->find_many();

                foreach ($feeds as $feed) {
                    $articles = \ORM::for_table('articles')->table_alias('a')->select('a.id')
                            ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
                            ->where('af.feed_id', $feed->id)
                            ->find_array();
//                    foreach ($articles as $article) {
//                        \ORM::for_table('user_articles')
//                            ->create(array('user_id' => $user->id, 'article_id' => $article->id))
//                            ->save();
//                    }
                    if ($articles) {

                        foreach ($articles as &$article) {
                            $article['user_id'] = $user->id;
                            $article['article_id'] = $article['id'];
                            unset($article['id']);
                        }

                        $datafields = array('user_id', 'article_id');

                        $pdo = \ORM::get_db();

                        $insert_values = array();
                        $question_marks = [];
                        foreach ($articles as $d) {
                            $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
                            $insert_values = array_merge($insert_values, array_values($d));
                        }

                        $sql = "INSERT INTO user_articles (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

                        $stmt = $pdo->prepare($sql);

                        try {
                            $stmt->execute($insert_values);
                        } catch (\PDOException $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="Confirm Unsubscribe from email")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/email/registerconfirm")
     * @ApiParams(name="token", type="int", nullable=false, description="Token Code for Unsubscribe")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function registerConfirm($token) {

        $user = \ORM::for_table('users')->where('email_token', $token)->find_one();

        if ($user) {
            $user->set('email_notify', 1)->save();

            $userSettings = \ORM::for_table('user_settings')->where('user_id', $user->id)->find_one();
            $userSettings->set('active', 1)
                    ->set('content', helpers\tokenGenerator())
                    ->set('confirmed', 1)
                    ->save();

            $pdo = \ORM::get_db();

            $query = $pdo->prepare('INSERT INTO user_institutions SELECT * FROM temp_user_institutions
                              WHERE user_id = :user_id; DELETE FROM temp_user_institutions WHERE user_id = :user_id');
            $query->execute(array('user_id' => $user->id));

            $user_institutions = \ORM::for_table('user_institutions')->where('user_id', $user->id)->find_many();

            foreach ($user_institutions as $user_institution) {
                $feeds = \ORM::for_table('feeds')->where('institution_id', $user_institution->institution_id)->find_many();
                foreach ($feeds as $feed) {
                    $articles = \ORM::for_table('articles')->table_alias('a')->select('a.id')
                                    ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
                                    ->where('af.feed_id', $feed->id)->find_array();

                    if ($articles) {

                        foreach ($articles as &$article) {
                            $article['user_id'] = $user->id;
                            $article['article_id'] = $article['id'];
                            unset($article['id']);
                        }

                        $datafields = array('user_id', 'article_id');

                        $pdo = \ORM::get_db();

                        $insert_values = array();
                        $question_marks = [];
                        foreach ($articles as $d) {
                            $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
                            $insert_values = array_merge($insert_values, array_values($d));
                        }

                        $sql = "INSERT INTO user_articles (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

                        $stmt = $pdo->prepare($sql);

                        try {
                            $stmt->execute($insert_values);
                        } catch (\PDOException $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="User frontend subscribe")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/unsubscribe")
     * @ApiParams(name="email", type="string", nullable=false, description="User email")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function unSubscribe($email) {
        $user = \ORM::for_table('user_settings')->where('content', $email)->where('type', USER_EMAIL)->find_one();
        if ($user) {
            $user = \ORM::for_table('users')->where('id', $user->user_id)->find_one();

            $adrress = 'http://' . $_SERVER['SERVER_NAME'] . '/email/unsubscribeconfirm?=&token=' . $user->email_token;

            $sent = \API\Application::sendMail(
                            'Official Alert', "Va rugăm accesaţi acest <a href='$adrress'>link</a> pentru a confirma dezabonarea."
            );
            ApiResult::setMessage('Vă rugăm să confirmați dezabonarea de pe email');
        } else {
            ApiResult::setCode(0);
            ApiResult::setMessage('Email-ul nu a fost găsit');
        }
    }

    /**
     * @ApiDescription(section="Frontend", description="Confirm Unsubscribe from email")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/email/unsubscribeconfirm")
     * @ApiParams(name="token", type="int", nullable=false, description="Token Code for Unsubscribe")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function unSubscribeConfirm($token) {

        $user = \ORM::for_table('users')->where('email_token', $token)->find_one();

        if ($user) {
            $user = \ORM::for_table('users')->where('email_token', $token)->find_one();
            $user->delete();

            return true;
        } else {
            return false;
        }
    }

    public function placeholders($text, $count = 0, $separator = ",") {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    public function createUser($email, $password, $firstName, $surname) {
        $password = sha1($email . $password);
        //the user is inserted when there are no duplicates
        \ORM::for_table('users')->create()
                ->set('push_notify', 1)
                ->set('email_token', helpers\tokenGenerator())
                ->set('email', $email)
                ->set('password', $password)
                ->set('first_name', $firstName)
                ->set('surname', $surname)
                ->set('email_notify', 0)
                ->save();

        $id = \ORM::get_db()->lastInsertId();

        \ORM::for_table('user_settings')->create()
                ->set('user_id', $id)
                ->set('content', helpers\tokenGenerator())
                ->set('type', 4)
                ->set('confirmed', 0)
                ->save();

        $user = \ORM::for_table('user_settings')
                        ->join('users', array('users.id', '=', 'user_settings.user_id'))
                        ->where('users.id', $id)->find_array();

        $institutions = \ORM::for_table('institutions')->select('id')->find_array();

        foreach ($institutions as &$institution) {
            $institution['user_id'] = $id;
            $institution['institution_id'] = $institution['id'];
            unset($institution['id']);
        }

        $datafields = array('user_id', 'institution_id');

        $pdo = \ORM::get_db();

        $insert_values = array();
        $question_marks = [];
        foreach ($institutions as $d) {
            $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
            $insert_values = array_merge($insert_values, array_values($d));
        }

        $sql = "INSERT INTO user_institutions (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute($insert_values);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

        foreach ($institutions as $institution) {

            $feeds = \ORM::for_table('feeds')->where('institution_id', $institution['institution_id'])->find_many();

            foreach ($feeds as $feed) {

                $articles = \ORM::for_table('articles')->table_alias('a')->select('a.id')
                        ->join('article_feeds', ['af.article_id', '=', 'a.id'], 'af')
                        ->where('af.feed_id', $feed->id)
                        ->find_array();

                if ($articles) {

                    foreach ($articles as &$article) {
                        $article['user_id'] = $id;
                        $article['article_id'] = $article['id'];
                        unset($article['id']);
                    }

                    $datafields = array('user_id', 'article_id');

                    $pdo = \ORM::get_db();

                    $insert_values = array();
                    $question_marks = [];
                    foreach ($articles as $d) {
                        $question_marks[] = '(' . $this->placeholders('?', sizeof($d)) . ')';
                        $insert_values = array_merge($insert_values, array_values($d));
                    }

                    $sql = "INSERT INTO user_articles (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

                    $stmt = $pdo->prepare($sql);

                    try {
                        $stmt->execute($insert_values);
                    } catch (\PDOException $e) {
                        echo $e->getMessage();
                    }
                }
            }
        }

        return $user[0]['email_token'];
    }

    public function updateToken($token) {
        $user = \ORM::for_table('users')->where('email_token', $token)->find_one();
        if ($user) {
            $user->set('email_token', helpers\tokenGenerator());
            $user->save();
        }
    }

    public function loginFB($data) {
        if (isset($data['id'])) {
            $user = [];
            $user = \ORM::for_table('user_settings')
                ->join('users', ['users.id', '=', 'user_settings.user_id'])
                //->where('users.email', $data['email'])
                ->where('content',$data['id'])
                ->where('type', 4)
                ->where('confirmed', 1)
                ->find_array();

            if (count($user) == 0) {

                $user = \ORM::for_table('users')->create()
                        ->set('push_notify', 1);
                if (isset($data['email'])) {
                    $user->set('email', $data['email']);
                }
                
                
                if(isset($data['name']))
                {
                    $name = explode(' ', $data['name']);
                    if (count($name) > 1) {
                        $user->set('first_name', $name[0]);
                        $user->set('surname', $name[1]);
                    }
                }
                
                $user->save();

                $id = \ORM::get_db()->lastInsertId();
                
                \ORM::for_table('user_settings')->create()
                        ->set('user_id', $id)
                        ->set('content', $data['id'])
                        ->set('type', 4)
                        ->set('confirmed', 1)
                        ->save();
                
                //load inserted user
                
                 $user = \ORM::for_table('user_settings')
                ->join('users', ['users.id', '=', 'user_settings.user_id'])
                ->where('users.email', $data['email'])
                ->where('content',$data['id'])
                ->where('type', 4)
                ->where('confirmed', 1)
                ->find_array();
                                
            } else {

                    unset($user[0]['password']);
                    unset($user[0]['email']);

            }
            ApiResult::setData($user);
        }
    }

}
