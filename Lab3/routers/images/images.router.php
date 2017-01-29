<?php

use API\ApiResult;
use API\ApiInput;
use \models\Feed;
use \models\User;
use \models\Backend;

$app->get('/file/', function () use ($app, $log) {

    $get = $app->request->get();

    $file = dirname($_SERVER["DOCUMENT_ROOT"]).'/share/files/'.$get['file_name'];

    if(@file_get_contents($file)){

        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($file));
        echo file_get_contents($file);
        die;
    }
     return false;

});