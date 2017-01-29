<?php
include_once "../lib/API/ApiResult.php";
include_once "../helpers/common.php";

$token = \helpers\tokenGenerator();

if(!isset($_GET['token'])){
    \API\ApiResult::setCode(0);
    \API\ApiResult::setMessage('403 Forbidden');
}

if($token == $_GET['token']) {

    try {

        $feedUrl = htmlspecialchars(urldecode($_GET['feed_url']));

        $content = file_get_contents($feedUrl);

        $rss = new \SimpleXmlElement(trim($content));

        $count = count($rss->channel->item) > 0;
	
		
        if (!$count) {
            \API\ApiResult::setCode(0)->setMessage('Failed');
        }


    } catch (Exception $e) {
        \API\ApiResult::setCode(0)->setMessage($e->getMessage());
    }

    echo \API\ApiResult::getJSON();

}