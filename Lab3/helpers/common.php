<?php

namespace helpers;


if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            echo "<pre>";
            var_dump($x);
            echo "</pre>";
        }, func_get_args());
        die;
    }
}

if (!function_exists('set_var')) {
    /**
     * @param $var
     * @param string $default
     * @return string
     */
    function set_var(&$var, $default = '')
    {
        if (isset($var))
            return $var;
        else return $default;
    }
}

if (!function_exists('httpRequest')) {

    function httpRequest($url)
    {
        try {
            $ch = curl_init();

            if (FALSE === $ch)
                throw new \Exception('failed to initialize');

            curl_setopt($ch = curl_init(), CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT,30);
            curl_setopt($ch, CURLOPT_ENCODING,'');
            $response = curl_exec($ch);

            if (FALSE === $response)
                throw new \Exception(curl_error($ch), curl_errno($ch));
            curl_close($ch);
            // ...process $content now
            return trim($response);

        } catch (\Exception $e) {
           @ trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
        }

        return false;
    }
}

if (!function_exists('moreThan24h')) {

    function moreThan24h($date1)
    {
        $timestamp = strtotime($date1); //1373673600

        // getting current date
        $cDate = strtotime(date('Y-m-d H:i:s'));

        // Getting the value of old date + 24 hours
        $oldDate = $timestamp + 86400; // 86400 seconds in 24 hrs

        return ($oldDate < $cDate);
    }
}
if (!function_exists('user_sh1')){

    function user_sh1($email, $password)
    {
        return sha1($email.$password);
    }

}

if (!function_exists('placeholders')){

    function placeholders($text, $count=0, $separator=","){
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

}

if (!function_exists('isMultidimensionalArray')) {
    //check if $data is multidimensional array
    function isMulti($array)
    {
        return !((count($array) == count($array, COUNT_RECURSIVE)));
    }
}

if (!function_exists('randomString')) {
    function randomString($length = 20)
    {
        //generate a random string
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
if (!function_exists('randomString')) {
    function tokenGenerator()
    {
        return sha1('slim'.date('y-m-d H:i'));
    }
}
if (!function_exists('benchmark')) {
    function benchmark($time_start = 0)
    {
        if($time_start == 0){
            return microtime(true);
        }else{
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            return "Did nothing in $time seconds\n";
        }

    }
}

if (!function_exists('flash')) {
    function flash( $name = '', $message = '' )
    {
        //We can only do something if the name isn't empty
        if( !empty( $name ) )
        {
            //No message, create it
            if( !empty( $message ) )
            {
                if( !empty( $_COOKIE[$name] ) )
                {
                    unset( $_COOKIE[$name] );
                }
                setcookie($name,$message, null, '/');
                //$_COOKIE[$name] = $message;
            }
        }
    }
}

if (!function_exists('getBaseUrl')) {
    function getBaseUrl()
    {
        $base_url = ((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base_url .= "://".$_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

        return $base_url;
    }

}



