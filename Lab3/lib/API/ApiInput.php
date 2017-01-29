<?php

namespace API;

use Respect\Validation\Exceptions\NestedValidationExceptionInterface;
use Respect\Validation\Validator as v;
use \helpers;
use Slim\Slim;

/**Response Class
 * Class ApiInput
 * @package API
 */
class ApiInput
{

    private static $error = array();

    /**Institution checker
     * @param $data
     * @return bool
     */

    public static function checkInstitution($data)
    {
    
        return self::checkFields($data, array(
                'name' => v::notEmpty(),
                'url' => v::notEmpty(),
                'type' => v::notEmpty()
            )
        );

    }

    public static function checkDashboardRegister($data)
    {
        return self::checkFields($data, array(
                'email' => v::notEmpty()->email(),
                'first_name' => v::notEmpty()->string(),
                'surname' => v::notEmpty()->string(),
                'password' => v::notEmpty(),
                'verify-password' => v::notEmpty()
            )
        );

    }
    public static function checkInstitutions($data)
    {
        //checks if institution id is valid
        $institutions = \ORM::for_table('institutions')->find_many();
        $ids = [];
        foreach ($institutions as $institution) {
            $ids[] = $institution->id;
        }

        return self::checkFields($data, array(
                'institutions' => v::notEmpty()->arr()->each(v::in($ids)),
            )
        );
    }
    /**Institution checker
     * @param $data
     * @return bool
     */

    public static function checkUserInstitutions($data)
    {
        return self::checkFields($data, array(
                'status' => v::numeric()->in(array(0, 1)),
                'institution' => v::notEmpty()->int()
            )
        );

    }

    /**register checker
     * @param $data
     * @return bool
     */

    public static function checkArticles($data)
    {        
        return self::checkFields($data, array(
                'start' => v::notEmpty()->int(),
                'limit' => v::notEmpty()->int(),
            )
        );

    }

    public static function checkResetPassword($data)
    {
        return self::checkFields($data, array(
                'old_password' => v::notEmpty()->string(),
                'new_password' => v::notEmpty()->string(),
                'verify_password' => v::notEmpty()->string(),
            )
        );

    }

    /**register checker
     * @param $data
     * @return bool
     */

    public static function checkRegister($data, $columns)
    {
        return self::oneOf($data, $columns)
            ->checkFields($data, array(
                    'token' => v::oneOf(v::nullValue(), v::string())
                )
            );

    }


    /**push checker
     * @param $data
     * @return bool
     */

    public static function checkPush($data)
    {
        return self::checkFields($data, array(
                'push' => v::numeric()->in(array(0, 1))
            )
        );

    }
    public static function checkArticleExist($data)
    {
        $ids = [];
        $articles = \ORM::for_table('articles')->find_many();
        foreach ($articles as $article) {
            $ids[] = $article->id;
        }

        return self::checkFields($data, array(
                'article_id' => v::numeric()->in($ids)
            )
        );

    }
    /**feed input checker
     * @param $data
     * @return bool
     */

    public static function checkFeedInstitution($data)
    {
        //checks if institution id is valid
        $institutions = \ORM::for_table('institutions')->find_many();
        foreach ($institutions as $institution) {
            $ids[] = $institution->id;
        }

        return self::checkFields($data, array(
                'institution' => v::notEmpty()->arr()->each(v::in($ids)),
            )
        );
    }

    public static function checkFeed($data)
    {
        return self::checkFields($data, array(
                'id' => v::notEmpty()->string(),
                'url' => v::notEmpty()->string()
            )
        );
    }

    public static function checkInputsPassword($data)
    {
        return self::checkFields($data, array(
                'password' => v::notEmpty()->string(),
                'new_password' => v::notEmpty()->string(),
                'verify_password' => v::notEmpty()->string()
            )
        );
    }

    public static function checkSubscriber($data)
    {
        return self::checkFields($data, array(
                'id' => v::notEmpty()->string(),
                'email' => v::notEmpty()->email(),
                'status' => v::numeric()->in(array(0, 1))
            )
        );
    }

    public static function checkAdminProfile($data)
    {
        return self::checkFields($data, array(
                'password' => v::notEmpty()->string(),
                'new_password' => v::notEmpty()->string(),
                'verify_password' => v::notEmpty()->string(),
            )
        );
    }

    public static function checkInt($data)
    {
        return self::checkFields($data, array(
                'id' => v::notEmpty()->int(),
            )
        );
    }
    public static function checkUser($data)
    {
        return self::checkFields($data, array(
                'email' => v::notEmpty()->string(),
                'password' => v::notEmpty()->string()
            )
        );
    }
    public static function checkArticle($data)
    {

        return self::checkFields($data, array(
                'title' => v::notEmpty()->string()
            )
        );
    }

    public static function checkFeeds($data)
    {
        $ids = [];
        $feeds = \ORM::for_table('feeds')->find_many();
        foreach ($feeds as $feed) {
            $ids[] = $feed->id;
        }

        return self::checkFields($data, array(
                'feeds_id' =>  v::notEmpty()->arr()->each(v::in($ids))
            )
        );
    }
    public static function checkToken($data)
    {
        return self::checkFields($data, array(
                'token' => v::notEmpty()->string()
            )
        );
    }

    public static function checkEmail($data)
    {
        $err = [];
        if (!isset($data['email'])) {
            $err[] = ['key' => 'email', 'message' => 'Câmpul email obligatoriu.'];
        }
        if(isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $err[] = ['key' => 'email', 'message' => 'Email format greşit'];
        }

        if (!empty($err)) {

            ApiResult::setCode(102)
                ->setMessage('Validation error')
                ->setData($err);

            return false;
        }
        return true;
    }
    public static function checkUserSubscribe($data)
    {

        $err = [];
        if (!isset($data['email'])) {
            $err[] = ['key' => 'email', 'message' => 'Câmpul email obligatoriu.'];
        }
        if(isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $err[] = ['key' => 'email', 'message' => 'Email format greşit'];
        }
       
        if(!isset($data['more'])){
            $err[] = ['key' => 'more', 'message' => 'More field required'];
        }
        if(isset($data['more']) && (int) $data['more'] > 1 ){
            $err[] = ['key' => 'more', 'message' => 'More field must be 0 or 1'];
        }
        if (!empty($err)) {

            ApiResult::setCode(102)
                ->setMessage('Validation error')
                ->setData($err);

            return false;
        }
        return true;
    }

    /**check for valid link add
     * @param $data
     * @return bool
     */
    public static function checkFeedUrl($data)
    {

        //checks if institution id is valid
        $institutions = \ORM::for_table('institutions')->find_many();
        foreach ($institutions as $institution) {
            $ids[] = $institution->id;
        }

        //checks if rss is valid
        return self::checkFields($data, array(
                'url' => v::notEmpty()->callback(function () use ($data) {
                    return ApiInput::checkValidRss($data['url']);
                }),
                'institution' => v::notEmpty()->in($ids)
            )
        );

    }

    public static function checkFeedExisting($feedsId)
    {

        //checks if category id is valid
        $feeds = \ORM::for_table('feeds')->find_many();
        foreach ($feeds as $feed) {
            $ids[] = $feed->id;
        }

        return self::checkFields($feedsId, [
                'feeds' => v::notEmpty()->arr()->each(v::in($ids)),
            ]
        );

    }

    public static function checkCategoryExisting($categoryId)
    {
        //checks if category id is valid
        $categories = \ORM::for_table('categories')->find_many();
        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $category->id;
        }

        return self::checkFields($categoryId, array(
                'category_id' => v::notEmpty()->numeric()->in($ids),
            )
        );

    }

    /**check for valid rss
     * @param $url
     * @return bool
     */
    public static function checkValidRss($url)
    {

           $content = file_get_contents($url);
           $content = trim($content);
           $rss = new \SimpleXmlElement($content) ;
        
        return count($rss->channel->item) > 0;
    }

    public static function validateFeed($feedUrl) {


        $rssValidator = $_SERVER['HTTP_HOST'].'/feedValidator.php?feed_url='.urlencode($feedUrl).'&token='.helpers\tokenGenerator();
        $rssValidationResponse = helpers\httpRequest($rssValidator);
        $json = json_decode($rssValidationResponse);
        
        if( $json && $json->code == 1 ){

            return true;
        } else {
            return false;
        }
    }


    /**category checker
     * @param $data
     * @return bool
     */
    public static function checkCategory($data)
    {
        return self::checkFields($data, array(
                'name' => v::notEmpty()->string()
            )
        );

    }

    /**
     * @param $data
     * @return bool
     */
    public static function checkCategoryId($data)
    {
        return self::checkFields($data, array(
                'id' => v::notEmpty()->string(),
                'name' => v::notEmpty()->string()
            )
        );

    }

    //-------------------------------------------------------------------------------------------------------------------


    /**if at least one of fields is available
     * @param $data
     * @param $fields
     * @return static
     */
    private static function oneOf($data, $fields)
    {

        $result = FALSE;
        $error = array();

        foreach ($fields as $field) {

            if (!helpers\set_var($data[$field])) {
                $error[] = array('field' => $field, 'text' => 'required');
            } else {
                $result = TRUE;
            }
        }

        if (!$result) {
            self::$error = $error;
        }


        return new Static;
    }


    /**general checker
     * @param $data
     * @param $fields
     * @return bool
     */

    private static function checkFields($data, $fields)
    {
        $error = self::$error;
        $validator = NULL;
        $k = 0;

        //rule chaining
        foreach ($fields as $key => $rule) {

            //custom message
            (is_array($rule)) && $rule = $rule['rule'];


            if ($k++) {
                $validator = $validator->key($key, $rule);
            } else {
                $validator = v::key($key, $rule);
            }
        }

        try {

            $validator->assert($data);

            //message formatting
        } catch (NestedValidationExceptionInterface $e) {

            $fieldsToCheck = (array_keys($fields));

            foreach ($fields as $key => $rule) {
                if (is_array($rule)) {
                    $fieldsToCheck[$key] = $rule['message'];
                }
            }

            foreach ($e->findMessages($fieldsToCheck) as $field => $message) {
                $message && $error[] = array('field' => $field, 'text' => $message);
            }

        }

        if (!empty($error)) {

            ApiResult::setCode(102)
                ->setMessage('Validation error')
                ->setData($error);

            return false;
        }

        return true;
    }


    /**decode json input
     * @param $data
     * @return mixed
     */
    public static function asArray($data)
    {

        $output = array();

        if (!empty($data)) {

            if (is_string($data)) {
                parse_str($data, $output);
            } else {
                $output = $data;
            }

            foreach ($output as &$el) {

                $el = str_replace(array('\'', '"'), '', $el);
                $el = json_decode($el, true);
            }

        }

        return $output;
    }

    /**Empty default
     * @param $data
     * @param $fields
     * @return array
     */

    public static function defaultValue($data, $fields)
    {

        $result = array();
        foreach ($fields as $field) {
            $result[$field] = helpers\set_var($data[$field]);
        }

        return $result;
    }

    /**strip_tag but preserve content
     * @param $string
     * @return mixed|string
     */
    public static function rip_tags($string, $limit = 200, $read_more = true)
    {

        // ----- remove HTML TAGs -----
        $string = preg_replace('/<[^>]*>/', ' ', $string);

        // ----- remove control characters -----
        $string = str_replace("\r", '', $string);    // --- replace with empty space
        $string = str_replace("\n", ' ', $string);   // --- replace with space
        $string = str_replace("\t", ' ', $string);   // --- replace with space

        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));

        return self::read_more($string, $limit, $read_more);

    }

    /**word limiter
     * @param $content
     * @param $limit
     * @return array|string
     */
    public static function read_more($content, $limit, $read_more)
    {

        $content = strip_tags($content);
        $string = str_replace("&#039;", "'", $content);
        if($read_more){
            $content = trim(mb_ereg_replace("/\s+/", " ", $string));
            $content = explode(' ', $content, $limit);
            array_pop($content);
        }else{
            $content = mb_ereg_replace("/\s+/", " ", $string);
            $content = explode(' ', $content, $limit);
        }

        if(!empty($content) && $read_more){
            array_push($content, '...');
        }

        $content = implode(' ', $content);

        return $content;
    }
    
    //check if uploaded file is image
    public static function checkImage($file){
       
        $result = false;
        if (isset($file['type'])) {
            $exploded_type = explode('/', $file['type']);
            if (isset($exploded_type[0])) {
                if ($exploded_type[0] == 'image') {
                    $result =  true;
                }
            }
        }
        return $result;
        
    }
    
    //move file from temp location to upload folder
    public static function uploadTestimonialImage($file) {
        if (isset($file['name'])) {
            $uploadfile = '../public/uploads/testimonials';

            $exploded_file = explode('.', $file['name']);
            if (isset($exploded_file[1])) {
                $extension = $exploded_file[1];
            }
            $title = rand(10000, 999999).'.' . $extension;
            $uploadfile .= '/' . $title;
            move_uploaded_file($file['tmp_name'], $uploadfile);
            ApiResult::setData(['file'=>$title]);
            return $title;
        }
        return false;
    }
    
    
      //move file from temp location to upload folder
    public static function uploadBlogImage($file) {
        if (isset($file['name'])) {
            $uploadfile = '../public/uploads/blog';

            $exploded_file = explode('.', $file['name']);
            if (isset($exploded_file[1])) {
                $extension = $exploded_file[1];
            }
            $title = rand(10000, 999999).'.' . $extension;
            $uploadfile .= '/' . $title;
            move_uploaded_file($file['tmp_name'], $uploadfile);
            ApiResult::setData(['file'=>$title]);
            return $title;
        }
        return false;
    }
    
    public static function checkTestimonials($data)
    {
        return self::checkFields($data, array(
                'name' => v::notEmpty()->string(),
                'content' =>v::notEmpty()->string()
            )
        );
    }
    
     public static function checkBlog($data)
    {
        return self::checkFields($data, array(
                'title' => v::notEmpty()->string(),
                'content' =>v::notEmpty()->string()
            )
        );
    }

}
