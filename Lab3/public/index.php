<?php

require_once dirname(__FILE__) . '/../bootstrap.php';

use API\Exception;

//----------------------------------------------------------------------------------------------------------------------
//GENERAL ROUTES
// docs
$app->get(
    '/apiDoc',
    function () use ($app, $log) {
        $app->redirect('apidoc.php');
    }
);


//frontend
$app->get(
    '/',
    function () use ($app, $log) {
        $app->render('frontend/index.php',array('baseUrl' => '/templates/frontend/'));
    }
);
$app->group(
    '/email',
    function () use ($app, $log) {
        $routers = glob(dirname(__FILE__) . "/../routers/email/*.router.php");

        foreach ($routers as $router) {
            require_once $router;
        }

    }
);
$app->group(
    '/images',
    function () use ($app, $log) {
        $routers = glob(dirname(__FILE__) . "/../routers/images/*.router.php");

        foreach ($routers as $router) {
            require_once $router;
        }

    }
);
//backend
$app->get(
    '/backend',
    function () use ($app, $log) {
        $app->render('backend/index.php',array('baseUrl' => '/templates/backend/'));
    }
);


// Group for API Versions
$app->group(
    "/v1",
    // API Methods
    function () use ($app, $log) {

        $routers = glob(dirname(__FILE__) . "/../routers/v1/*.router.php");

        foreach ($routers as $router) {
            require_once $router;
        }
    }
);

//----------------------------------------------------------------------------------------------------------------------
// JSON friendly errors
// NOTE: debug must be false
// or default error template will be printed
$app->error(function (\Exception $e) use ($app, $log) {

    $mediaType = $app->request->getMediaType();

    $isAPI = (bool)preg_match('|^/v.*$|', $app->request->getPath());

    // Standard exception data
    $error = array(
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
        'data' => array(array(
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ))

    );

    \ORM::for_table('logs')->create()
        ->set('code', $e->getCode())
        ->set('message', $e->getMessage())
        ->set('file', $e->getFile())
        ->set('line', $e->getLine())
        ->save();


    if (!in_array(
            get_class($e),
            array('API\\Exception', 'API\\Exception\ValidationException')
        )
        && 'production' === $app->config('mode')
    ) {
        $error['message'] = 'There was an internal error';
        unset($error['file'], $error['line']);
    }

    // Custom error data (e.g. Validations)
    if (method_exists($e, 'getData')) {
        $errors = $e->getData();
    }

    if (!empty($errors)) {
        $error['errors'] = $errors;
    }

    $log->error($e->getMessage());
    if ('application/json' === $mediaType || true === $isAPI) {
        $app->response->headers->set(
            'Content-Type',
            'application/json'
        );
        echo json_encode($error, JSON_PRETTY_PRINT);
    } else {
        echo '<html>
        <head><title>Error</title></head>
        <body><h1>Error: ' . $error['code'] . '</h1><p>'
            . $error['message']
            . '</p></body></html>';
    }
    return false;
});

/// Custom 404 error
$app->notFound(function () use ($app) {

    $mediaType = $app->request->getMediaType();

    $isAPI = (bool)preg_match('|^/v.*$|', $app->request->getPath());


    if ('application/json' === $mediaType || true === $isAPI) {

        $app->response->headers->set(
            'Content-Type',
            'application/json'
        );

        echo json_encode(
            array(
                'code' => 404,
                'message' => 'Not found',
                'data' => array()
            ),
            JSON_PRETTY_PRINT
        );

    } else {
        echo '<html>
        <head><title>404 Page Not Found</title></head>
        <body><h1>404 Page Not Found</h1><p>The page you are
        looking for could not be found.</p></body></html>';
    }
});

$app->run();
