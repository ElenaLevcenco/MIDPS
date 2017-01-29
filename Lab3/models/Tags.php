<?php

namespace models;

use API\ApiInput;
use API\ApiResult;
use helpers;

class Tags {

    protected $configs;
    protected $user;

    public function __construct() {
        
    }   

    /**
     * @ApiDescription(section="User", description="register user/email")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/user/login")
     * @ApiParams(name="token", type="string", nullable=false, description="Device token")
     * @ApiReturn(type="object", sample="{
     *  'code':'int',
     *  'message':'string',
     *  'data':'array'
     * }")
     */

    public function add( ) {

    }

    public function edit() {

    }

    public function delete() {

    }
    

}
