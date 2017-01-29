<?php
namespace API\Middleware;

class RateLimit extends \Slim\Middleware
{

    protected $cache;
    public function __construct($root = '')
    {
        $this->root = $root;
        $this->max = 3600; // Requests per hour

        
    }

    public function call()
    {
        $response = $this->app->response;
        $request = $this->app->request;

        if ($max = $this->app->config('rate.limit')) {
            $this->max = $max;
        }

        // Activate on given root URL only
        if (preg_match(
            '|^' . $this->root . '.*|',
            $this->app->request->getResourceUri()
        )) {

            $token = $request->headers->get('userToken');


            if(isset($token)){
                $this->checkToken($token);
            }else{
                $res = $this->app->response();
                $res->status(403);
            }
            // Use API key from the current user as ID
            if ($key = $token) {
                $data = $this->fetch($key);
                if (false === $data) {

                    // First time or previous perion expired,
                    // initialize and save a new entry

                    $remaining = ($this->max -1);
                    $reset = 3600;

                    $this->save(
                        $key,
                        array(
                            'remaining' => $remaining,
                            'created' => time()
                        ),
                        $reset
                    );
                } else {

                    // Take the current entry and update it

                    $remaining = (--$data['remaining'] >= 0)
                        ? $data['remaining'] : -1;

                    $reset = (($data['created'] + 3600) - time());

                    $this->save(
                        $key,
                        array(
                            'remaining' => $remaining,
                            'created' => $data['created']
                        ),
                        $reset
                    );
                }

                // Set rating headers

                $response->headers->set(
                    'X-Rate-Limit-Limit',
                    $this->max
                );

                $response->headers->set(
                    'X-Rate-Limit-Reset',
                    $reset
                );

                $response->headers->set(
                    'X-Rate-Limit-Remaining',
                    $remaining
                );

                // Check if the current key is allowed to pass
                if (0 > $remaining) {

                    // Rewrite remaining headers
                    $response->headers->set(
                        'X-Rate-Limit-Remaining',
                        0
                    );

                    // Exits with status "429 Too Many Requests" (see doc below)
                    $this->fail();
                }


            } else {
                // Exits with status "429 Too Many Requests" (see doc below)
                $this->fail(403);
            }


        }

        $this->next->call();
        
    }
    protected function fetch($key)
    {
        $this->cache = phpFastCache();
        $this->cache->set($key, 'aaaaaaaaaaaaaaaaaaa', 0);

        \helpers\dd( $this->cache);
        return $this->cache->get($key);
    }
    
    protected function save($key, $value, $expire = 0)
    {

        $this->cache->set($key, $value, $expire);
    }
    //If Token is set verify in db
    protected function checkToken($token)
    {
        $res = $this->app->response();

        if ($user = \ORM::for_table('users')->where('token', $token)->find_one()) {
            return $user;
        }else{
            $res->status(403);
        }
    }
    /**
     * Exits with status "429 Too Many Requests"
     *
     * Work around on Apache's issue: it does not support
     * status code 429 until version 2.4
     * 
     * @link http://stackoverflow.com/questions/17735514/php-apache-silently-converting-http-429-and-others-to-500
     */
    protected function fail($param = 429)
    {
        if($param == 429){
            header('HTTP/1.1 429 Too Many Requests', false, 429);
        } elseif($param == 403){
            header('HTTP/1.0 403 Forbidden', false, 403);
        }

        
        // Write the remaining headers
        foreach ($this->app->response->headers as $key => $value) {
            header($key . ': ' . $value);
        }
        exit;
    }
}
