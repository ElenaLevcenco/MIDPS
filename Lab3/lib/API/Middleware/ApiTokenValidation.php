<?php
/**
 * Token Over HTTP Basic Authentication
 *
 * Use this middleware with your Slim Framework application
 * to require a user name or API key via HTTP basic auth
 * for all routes. No need for password.
 *
 * NOTE: the verify() protected method requires an ORM object,
 * Idiorm is the default.
 *
 *
 * @author Vito Tardia <vito@tardia.me>
 * @version 1.0
 * @copyright 2014 Vito Tardia
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new API\Middleware\TokenOverBasicAuth());
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace API\Middleware;

use API\ApiResult;

class ApiTokenValidation extends \Slim\Middleware
{

    protected $exceptions = [
        '/email/subscribeconfirm', 
        '/push', 
        '/parse', 
        '/dashboard/recovery',
        '/user/login', 
        '/user/subscribe', 
        '/backend/login', 
        '/dashboard/recovery/confirm',
        '/categories', 
        '/categories/last', 
        '/user/subscribeconfirm', 
        '/institutions/all',
        '/user/unsubscribe', 
        '/email/unsubscribeconfirm',
        '/dashboard/login',
        '/dashboard/register',
        '/testimonials/all',
        '/blog/all',
        '/post',
        '/dashboard/login/facebook',
        '/about/all',
        '/comments',
        '/check/captcha',
        '/debug/push'
    ];

    public function __construct($root = '')
    {
        $this->root = $root;
        foreach ($this->exceptions as &$exception) {
            $exception = $root . $exception;
        }
        
    }

    public function deny_access()
    {
        $res = $this->app->response();
        $res->status(401);

        ApiResult::setCode(0)
            ->setMessage('Access Denied')
            ->getJSON();

    }

    /**
     * Call
     *
     * This method will check the HTTP request headers for
     * previous authentication. If the request has already authenticated,
     * the next middleware is called. Otherwise,
     * a 401 Authentication Required response is returned to the client.
     *
     * @return  void
     */
    public function call()
    {
        $req = $this->app->request();

        if ((!in_array($req->getPathInfo(), $this->exceptions)
            && preg_match('|^' . $this->root . '.*|', $this->app->request->getResourceUri()))) {

            $user = $this->getUser($this->checkToken());

            if (!$user) {
                $this->deny_access();
            } else {

                $this->app->auth_user = $user;
                $this->next->call();
            }
        } else { //is root
            $this->next->call();
        }
    }

    public function getUser($token)
    {

        $user = \ORM::for_table('user_settings')
            ->join('users', array('users.id', '=', 'user_settings.user_id'))
            ->where('content', $token)
            //->where('type', 4)
            ->find_one();

        if ($user) {
            return $user;
        };
        return false;
    }

    public function checkToken()
    {
        $token = $this->app->request()->get('Usertoken');
        $token2 = $this->app->request()->get('userToken');
        if(isset($this->app->request->headers['Usertoken'])){

            return $this->app->request->headers['Usertoken'];
        }elseif(isset($this->app->request->headers['userToken'])){

            return $this->app->request->headers['userToken'];
        }elseif(isset($token)){
            return $token;
        }elseif(isset($token2)){
            return $token2;
        }else{
            return '';
        }

    }

}
