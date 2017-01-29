<?php

namespace API;

/**Response Class
 * Class ApiResult
 * @package API
 */

class ApiResult
{
    protected static $code = 1;
    protected static $message = 'Success';
    protected static $data = [];


    /**Code setter
     * @param $code
     * @return static
     */
    public static function setCode($code)
    {

        self::$code = $code;
        return new static;
    }

    /**Message setter
     * @param $message
     * @return static
     */
    public static function setMessage($message)
    {

        self::$message = $message;
        return new static;
    }

    /**Data setter
     * @param $data
     * @return static
     */
    public static function setData($data)
    {

        //check if $data is multidimensional array for response formatting
        count($data) && !\helpers\isMulti($data) &&  $data = array($data);

        self::$data = $data;
        return new static;
    }

    /**
     * json response
     */
    public static function getJSON()
    {
        echo json_encode([
            'code' => self::$code,
            'message' => self::$message,
            'data' => self::$data
        ], JSON_PRETTY_PRINT);
        return '';
    }

    public static function notFound()
    {

    }

}
