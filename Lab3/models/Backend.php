<?php

namespace models;

use API\ApiResult;
use \helpers;


class Backend
{

    protected $configs;
    protected $user;

    public function __construct($configs = '')
    {
        $this->configs = $configs;
        isset($configs['auth_user']) && $this->user = $configs['auth_user'];

    }


    /**
     * @ApiDescription(section="Backend", description="Gets dashboard data")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/backend/dashboard")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getDashboard()
    {

        $subscribers = \ORM::for_table('user_settings')->where_in('type', array(USER_ANDROID, USER_EMAIL))->count();
        $feeds = \ORM::for_table('feeds')->count();
        $articles = \ORM::for_table('articles')->count();
        $logs = \ORM::for_table('logs')->count();

        $data = array(
            'totalSubscribers' => $subscribers,
            'totalFeeds' => $feeds,
            'totalArticles' => $articles,
            'totalErrors' => $logs,
            'lastErrors' => $logs = \ORM::for_table('logs')->limit('5')->order_by_desc('time_stamp')->find_array()
        );

        ApiResult::setData($data);
    }

    /**
     * @ApiDescription(section="Backend", description="Gets subscribers with email")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/backend/subscribers")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getSubscribers($type)
    {
        //$subscribers = \ORM::for_table('user_settings')->where_in('type', $type)->find_array();

        $users = \ORM::for_table('users')
            ->select(array('users.id','users.email','user_settings.active'))
            ->join('user_settings', 'users.id = user_settings.user_id')
            ->where_in('user_settings.type', $type)
            ->find_array();

        if (!$users) {
            $users = array();
            ApiResult::setMessage('No subscribers');
        }

        ApiResult::setData($users);
    }

    /**
     * @ApiDescription(section="Backend", description="Edit Subscriber")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/backend/login")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiParams(name="password", type="string", nullable=false, description="password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function login($post)
    {
        $user = \ORM::for_table('user_settings')
            ->join('users', ['users.id', '=', 'user_settings.user_id'])
            ->where('users.password', sha1($post['email'].$post['password']))
            ->where('user_settings.type', 3)
            ->find_one();

        if($user){

            if($user->content == sha1($post['email'].$post['password'])){

                ApiResult::setData(['token'=> $user->content]);

            }else{
                ApiResult::setCode(0);
                ApiResult::setMessage('Logare nereușită');
            }
        }else{
            ApiResult::setCode(0);
            ApiResult::setMessage('Logare nereușită');
        }


    }

    /**
     * @ApiDescription(section="Backend", description="Edit Subscriber")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/backend/article")
     * @ApiParams(name="title", type="string", nullable=false, description="Article Title")
     * @ApiParams(name="description", type="string", nullable=false, description="Article Description")
     * @ApiParams(name="url", type="string", nullable=false, description="Article Url")
     * @ApiParams(name="image", type="string", nullable=false, description="Article Description")
     * @ApiParams(name="feeds_id", type="string", nullable=false, description="Feeds ID array")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function createArticle($post, $feedsId)
    {

        $query = \ORM::for_table('articles')->create()
            ->set('title',$post['title'])
            ->set('url',$post['url']);

            isset($post['description']) && $query->set('description',$post['description']);

            if(array_key_exists('file',$post)){

                $file = json_decode($post['file']);

                $fileName = uniqid();
                $filePath = $_SERVER['DOCUMENT_ROOT'].'/../share/files/'.$fileName.'.'.$file->type;

                file_put_contents($filePath, base64_decode($file->base64));
                $query->set('file_path', 'images/file/?file_name='.$fileName.'.'.$file->type);

            }

        $saved = $query->save();

        $articleId = \ORM::get_db()->lastInsertId('id');

        foreach ($feedsId as $feedId) {
            \ORM::for_table('article_feeds')->create()
                ->set('article_id',$articleId)
                ->set('feed_id',$feedId)
                //->set('article_type',2)
                ->save();
        }

        $users = \ORM::for_table('users')->table_alias('u')
            ->join('user_settings',['u.id','=','us.user_id'],'us')
            ->join('user_institutions',['u.id','=','ui.user_id'],'ui')
            ->join('feeds',['f.institution_id','=','ui.institution_id'],'f')

            ->where_in('f.id',$feedsId)
            ->where_not_equal('us.type',3)
            ->group_by('us.user_id')
            ->find_array();

        foreach ($users as $user) {

            \ORM::for_table('user_articles')->create()
                ->set('user_id',$user['user_id'])
                ->set('article_id',$articleId)
                ->set('email_sent',0)
                ->save();
        }

        if($saved){
            return true;
        }
        return false;
    }

    /**
     * @ApiDescription(section="Backend", description="Edit Subscriber")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/backend/subscriber")
     * @ApiParams(name="id", type="int", nullable=false, description="User id")
     * @ApiParams(name="email", type="string", nullable=true, description="Email")
     * @ApiParams(name="status", type="int", nullable=true, description="Status active 1 inactive 0")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function editSubscriber($put)
    {
        $user_settings = \ORM::for_table('user_settings')->where('user_id',$put['id'])->find_one();
        $user_settings->set('active', $put['status'])
            ->set('content', $put['email']);
        $user_settings->save();

    }

    /**
     * @ApiDescription(section="Backend", description="Adds user feed")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/backend/profile")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiParams(name="password", type="string", nullable=false, description="Password")
     * @ApiParams(name="new_password", type="string", nullable=false, description="New Password")
     * @ApiParams(name="verify_password", type="string", nullable=false, description="Verify Password")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */
    public function editProfile($put)
    {

        $user = \ORM::for_table('users')->where('id',$this->user->id)->find_one();
        $user_settings = \ORM::for_table('user_settings')->where('user_id',$this->user->id)->find_one();

        if(isset($put['email'])) $user->set('email',$put['email']);
        if(isset($put['new_password']) && isset($put['verify_password']) && !isset($put['password'])){
            ApiResult::setMessage('Please set password');
            ApiResult::setCode(0);
        }

        if(isset($put['new_password']) && isset($put['verify_password']) && isset($put['password'])){
            if ($put['new_password'] == $put['verify_password']) {

                if($user->password == sha1($user->email.$put['password'])){
                    $user->set('password',sha1($user->email.$put['new_password']));
                    $user_settings->set('content', sha1($user->email.$put['new_password']));
                }else{
                    ApiResult::setMessage('Password is incorrect');
                    ApiResult::setCode(0);
                }
            }else{
                ApiResult::setCode(0);
                ApiResult::setMessage('password does not match');
            }
        }

        $user->save();
        $user_settings->save();
    }

    /**
     * @ApiDescription(section="Backend", description="Edit Subscriber")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/backend/unsubscribe")
     * @ApiParams(name="id", type="int", nullable=false, description="User id")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function deleteSubscriber($id)
    {

        var_dump($id);
        $user = \ORM::for_table('users')->where('id', $id)->find_one();

        if($user){
            $user->delete();
            ApiResult::setCode(1);
            ApiResult::setMessage('Subscriber Deleted');
        }else{
            ApiResult::setCode(0);
            ApiResult::setMessage('Invalid User id');
        }
    }

    /**
     * @ApiDescription(section="Backend", description="Gets log errors")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/backend/logs")
     * @ApiParams(name="limit", type="int", nullable=false, description="limit")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data' : 'array'
     * }")
     */
    public function getLogs($get)
    {

        $logsQuery = \ORM::for_table('logs');

        if (helpers\set_var($get['limit'])) {
            $logsQuery->limit($get['limit']);
        };


        $logs = $logsQuery->order_by_desc('time_stamp')->find_array();

        if (!$logs) {
            $logs = array();
            ApiResult::setMessage('No logs');
        }

        ApiResult::setData($logs);
    }

    public function insertImagesDb($fileType, $fileName, $dbFilePath)
    {
        $pdo = \ORM::get_db();
        $query = "INSERT INTO TB_API_FILE (FILE_PATCH, NAME) VALUES (?,?)";
        $pdoPrep=$pdo->prepare($query);
        $pdoPrep->bindParam(1,$dbFilePath);
        $pdoPrep->bindParam(2,$fileName);
        $pdoPrep->execute();

        $file = \ORM::for_table('TB_API_FILE')
            ->select('TB_API_FILE.ID')
            ->order_by_desc('TB_API_FILE.ID')
            ->find_array();
        $fileId = $file[0]['ID'];


        $ticket = \ORM::for_table('TB_API_TICKET')
            ->select('TB_API_TICKET.ID')
            ->order_by_desc('TB_API_TICKET.ID')
            ->find_array();

        if($ticketID != ''){
            $ticketId = $ticketID;
        }else{
            $ticketId = $ticket[0]['ID'];
        }
        $query = "INSERT INTO TB_API_TICKET_FILES (ID_TICKET, ID_FILE) VALUES (?,?)";
        $pdoPrep=$pdo->prepare($query);
        $pdoPrep->bindParam(1,$ticketId);
        $pdoPrep->bindParam(2,$fileId);
        $pdoPrep->execute();

    }
}