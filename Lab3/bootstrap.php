<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

use API\Application;
use API\ApiResult;


// Init application mode
if (empty($_ENV['SLIM_MODE'])) {
    $_ENV['SLIM_MODE'] = (getenv('SLIM_MODE'))
        ? getenv('SLIM_MODE') : 'development';
}


// Init and load default files
$config = array();

$autoload = [
    "/share/config/{$_ENV['SLIM_MODE']}",//config
    "/helpers",//helpers
    "/models",//models
    "/configs"//configs (Facebook,...)
];


foreach ($autoload as $path) {

    $autoloadFiles =glob(dirname(__FILE__). "{$path}/*.php");

    foreach ($autoloadFiles as $file) {
        if (is_readable($file)) {
            require_once $file;
        }
    }
}


// Create Application
$app = new Application($config['app']);

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.level' => \Slim\Log::WARN,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.level' => \Slim\Log::DEBUG,
        'debug' => false
    ));
});

// Get log writer
$log = $app->getLog();

// Init database
try {

    if (!empty($config['db'])) {

        \ORM::configure($config['db']['dsn']);
        if (!empty($config['db']['username'])) {
            \ORM::configure('username', $config['db']['username']);
            \ORM::configure('password', $config['db']['password']);
        }
    }

} catch (\PDOException $e) {
    $log->error($e->getMessage());
}



//OPTION safe page
if($app->request->getMethod()=='OPTIONS'){

    ApiResult::setMessage('Options request');
    ApiResult::getJSON();
    die();

};



// Create Transport
$transport = Swift_MailTransport::newInstance();
// Create Mailer with our Transport.
$mailer = Swift_Mailer::newInstance($transport);
//set application mailer
Application::setMailer($mailer);

// Cache Middleware (inner)
//$app->add(new API\Middleware\Cache('/v1'));

// Parses JSON body
$app->add(new \Slim\Middleware\ContentTypes());

// Manage Rate Limit
//$app->add(new API\Middleware\RateLimit('/v1'));

// JSON Middleware
//$app->add(new API\Middleware\JSON('/v1'));

// Http Authentication
$app->add(new API\Middleware\HttpAuth('Protected Area','/v1'));

//Api Token Validation
$app->add(new API\Middleware\ApiTokenValidation('/v1'));

