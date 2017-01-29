<?php

use API\ApiResult;
use API\ApiInput;
use \models\Feed;
use \models\User;
use \models\Backend;

$app->get('/subscribeconfirm', function () use ($app, $log) {

    $get = $app->request->get();

    if(ApiInput::checkToken($get)){
        $user = new User();
        $result = $user->subscribeConfirm($get['token']);
        if(!$result){
            $app->halt(404, 'Not Found');
        }else{

            $userProfile = \ORM::for_table('users')->where('email_token', $get['token'])->find_one();

            $userProfile->set('email_notify',1);
            $userProfile->save();

            echo 'Abonare cu succes.';

            $sent = \API\Application::sendMail(
                array($userProfile->email),
                'Official Alert',
                "Abonare cu succes."
            );

            //Update Email Token
            $user->updateToken($get['token']);

            \helpers\flash( 'cookie_message', 'Abonare cu succes.' );
            header("Location: ".$app->config('base_url'));
            die();
        }
    }

});

$app->get('/unsubscribeconfirm', function () use ($app, $log) {

    $get = $app->request->get();

    if(ApiInput::checkToken($get)){
        $user = new User();
        $result = $user->unSubscribeConfirm($get['token']);
        if(!$result){


            \helpers\flash( 'cookie_message', 'Aţi fost deja dezabonat.' );
            header("Location: ".$app->config('base_url'));
            die();

        }else{
            //Update Email Token
            $user->updateToken($get['token']);
            \helpers\flash( 'cookie_message', 'Dezabonare cu succes.' );
            header("Location: ".$app->config('base_url'));
            die();
        }
    }

});

$app->get('/registerconfirm', function () use ($app, $log) {

    $get = $app->request->get();

    if(ApiInput::checkToken($get)){
        $user = new User();
        $result = $user->registerConfirm($get['token']);

        if(!$result){

            \helpers\flash( 'cookie_message', 'Aţi confirmat deja înregistrarea.' );
            header("Location: ".$app->config('base_url'));
            die();

        }else{
            //Update Email Token
            $user->updateToken($get['token']);

            \helpers\flash( 'cookie_message', 'Aţi confirmat cu success înregistrarea.' );
            header("Location: ".$app->config('base_url'));
            die();
        }
    }

});
$app->get('/recoveryconfirm', function () use ($app, $log) {

    $get = $app->request->get();

        $user = new User();
        $userProfile = $user->getUserByEmailToken($get['token']);

        if($userProfile){

            \helpers\flash( 'email_token', $userProfile->email_token );
            header("Location: ".$app->config('base_url')."user/#/page/changepassword");
            die();

        }


});