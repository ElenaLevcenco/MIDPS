<?php
namespace API\Middleware;

class HttpAuth extends \Slim\Middleware
{
    /**
     * @var string
     */
    protected $realm;
    protected $exceptions = [
        '/parse', 
        '/push',
        '/categories',
        '/categories/last', 
        '/email/unsubscribeconfirm',
        '/email/subscribeconfirm',
        '/user/subscribe',
        '/user/unsubscribe',
        '/institutions/all', 
        '/dashboard/login', 
        '/dashboard/register',
        '/dashboard/institutions',
        '/dashboard/articles',
        '/dashboard/password',
        '/dashboard/profile',
        '/dashboard/article',
        '/backend/login',
        '/backend/article', 
        '/backend/profile',
        '/backend/dashboard',
        '/dashboard/recovery',
        '/dashboard/recovery/confirm',
        '/testimonials/all',
        '/blog/all',
        '/post',
        '/about/all',
        '/comments',
        '/comment',
        '/check/captcha',
        '/comments/all',
        '/comment',
        '/debug/push'
        ];

    /**
     * Constructor
     *
     * @param string $realm The HTTP Authentication realm
     * @param string $root
     */
    public function __construct($realm = 'Protected Area', $root = '')
    {
        $this->realm = $realm;
        $this->root = $root;
        foreach ($this->exceptions as &$exception) {
            $exception = $root . $exception;
        }
    }

    /**
     * Deny Access
     *
     */
    public function deny_access()
    {

        $res = $this->app->response();
        $res->status(401);
        $res->header('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
    }

    /**
     * Authenticate
     *
     * @param   string $email The HTTP Authentication username
     * @param   string $password The HTTP Authentication password
     * @return bool
     */
    public function authenticate($user, $password)
    {

        if ((isset($user) && $user == $this->app->config('auth.user'))
            && isset($password) && $password == $this->app->config('auth.password')
        ) {
            return true;
        } else {
            return false;
        }


    }

    /**
     * Call
     *
     * This method will check the HTTP request headers for previous authentication. If
     * the request has already authenticated, the next middleware is called. Otherwise,
     * a 401 Authentication Required response is returned to the client.
     */
    public function call()
    {


        $basicAuth = $this->setup_php_http_auth();


        $req = $this->app->request;



        if ((!in_array($req->getPathInfo(), $this->exceptions) &&
            preg_match('|^' . $this->root . '.*|', $this->app->request->getResourceUri()))
        ) {


            //Verify if user is authenticated
            if (!$this->authenticate($basicAuth['user'], $basicAuth['password'])) {
                $this->deny_access();
            } else {

                $this->next->call();
            }

        } else //is root
        {
            $this->next->call();
        }


    }


    // attempt to support PHP_AUTH_USER & PHP_AUTH_PW if they aren't supported in this SAPI
//   known SAPIs that do support them:  apache, litespeed
    function setup_php_http_auth()
    {
        if ((PHP_SAPI === 'apache') || (PHP_SAPI === 'litespeed') || isset($_SERVER['PHP_AUTH_USER'])) {
            return array('user' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']);
        }

        foreach (array('HTTP_AUTHORIZATION', 'AUTHORIZATION', 'REMOTE_USER', 'REDIRECT_HTTP_AUTHORIZATION') as $key) {
            if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                list($type, $encoded) = explode(' ', $_SERVER[$key]);
                break;
            }
        }

        if (!isset($type) || ($type !== 'Basic')) {
            return;
        }

        list($user, $pass) = explode(':', base64_decode($encoded));

        return array('user' => $user, 'password' => $pass);

    }

}