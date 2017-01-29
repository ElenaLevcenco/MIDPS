<?php

use API\ApiResult;
use API\ApiInput;
use \models\Feed;
use \models\User;
use \models\Backend;
//use \helpers;

//-------------------------------------------------USER------------------------------------------------------------

$app->post('/user/login', function () use ($app, $log) {

    $columns = array('token');

    $post = ApiInput::defaultValue($app->request->post(), $columns);

    if (ApiInput::checkRegister($post, $columns)) {
        $user = new User();
        $user->register($post);
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------


$app->get('/user/articles', function () use ($app, $log) {
    $get = $app->request->get();

//    if (ApiInput::checkArticles($get)) {
    $feed = new User(array('auth_user' => $app->auth_user));
    $feed->getArticles($get);
//    }
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->get('/dashboard/articles', function () use ($app, $log) {
    $get = $app->request->get();

    $feed = new User(array('auth_user' => $app->auth_user));
    $feed->getDashboardArticles($get);

    return ApiResult::getJSON();
});
//-----------------------------------------------------------------------------------------------------------------


$app->put('/user/articles', function () use ($app, $log) {

    $data = ApiInput::asArray($app->request->put());

    $feed = new User(array('auth_user' => $app->auth_user));
    $feed->putArticles($data);

    return ApiResult::getJSON();
});

$app->get('/institutions', function () use ($app, $log) {

//    $data = $app->request->get();
    $feed = new Feed(array('auth_user' => $app->auth_user));


    $feed->getInstitutions();

    return ApiResult::getJSON();
});


$app->put('/institution', function() use ($app, $log) {
    
    $data = $app->request->put();
    
    
     //if (ApiInput::checkInstitution($data)) {
    $feed = new Feed(array('auth_user' => $app->auth_user));
    $feed->updateInstitution($data);
     

     return ApiResult::getJSON();
});
//---------------------------------------------------Feed-----------------------------------------------------------


$app->get('/parse', function () use ($app, $log) {

    $feed = new Feed();
    $feed->parseFeed($app->config('parse'));

    //response
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->get('/push', function () use ($app, $log) {

    $feed = new Feed(array('auth_user' => $app->auth_user));
    $feed->push();

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/user/push', function () use ($app, $log) {


    $data = $app->request->put();

    if (ApiInput::checkPush($data)) {

        $feed = new Feed(array('auth_user' => $app->auth_user));
        $feed->setPush($data);
    }


    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/feeds/categories', function () use ($app, $log) {

    //$put = ApiInput::defaultValue($app->request->put(), array('feeds'));
    $put = $app->request->put();
    $data = ApiInput::asArray($put);

    if (ApiInput::checkCategoryExisting($data) && ApiInput::checkFeedExisting($data)) {
        $feed = new Feed(array('auth_user' => $app->auth_user));
        $feed->editFeedsCategories($data);
        ApiResult::setData('success');
    } else {
        ApiResult::setCode(0);
        ApiResult::setMessage('Error');
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->delete('/feeds/categories', function () use ($app, $log) {

    $data = ApiInput::asArray($app->request->put());

    if (ApiInput::checkFeedExisting($data)) {
        ApiResult::setData('Feeds Exist');
    } else {
        ApiResult::setCode(0);
        ApiResult::setMessage('Feeds not exist');
    }
//    if(ApiInput::checkCategoryExisting($data['category_id']) && ApiInput::checkFeedExisting($data['feeds'])){
//        $feed = new Feed(array('auth_user' => $app->auth_user));
//        $feed->deleteFeedsCategories($data);
//    }


    return ApiResult::getJSON();
});
//-----------------------------------------------------------------------------------------------------------------

$app->put('/user/institution', function () use ($app, $log) {

    $put = $app->request->put();

    if (ApiInput::checkUserInstitutions($put)) {

        $feed = new User(array('auth_user' => $app->auth_user));
        $feed->updateUserInstitution($put['status'], $put['institution']);

        $feed = new Feed(array('auth_user' => $app->auth_user));
        $feed->getInstitutions();
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/dashboard/register', function () use ($app, $log) {

    $put = $app->request->put();
    $user = new User();

    if (ApiInput::checkDashboardRegister($put)) {
        if ($put['password'] == $put['verify-password']) {

            if (!$user->checkUserExist($put['email'])) {
                $user->clientRegister($put);
            } else {
                ApiResult::setCode(0);
                ApiResult::setMessage('Utilizator existent.');
            }
        } else {
            ApiResult::setCode(0);
            ApiResult::setMessage('Parola nu coincide.');
        }
    }

    return ApiResult::getJSON();
});

$app->get('/dashboard/profile', function () use ($app, $log) {

    $user = new User(['auth_user' => $app->auth_user]);
    $user = $user->getDashboardProfile();

    if ($user) {
        ApiResult::setData($user);
        ApiResult::setMessage('Success');
    } else {
        ApiResult::setCode(0);
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/dashboard/institutions', function () use ($app, $log) {

    $put = $app->request->put();
    $user = new User(['auth_user' => $app->auth_user]);
    $data = ApiInput::asArray($put);

    if (!empty($data['institutions'])) {
        $user->updateUserInstitutions($data['institutions']);
        //var_dump('Institutions Valid');
    } else {
        ApiResult::setCode(0);
        ApiResult::setMessage('Selectați cel puțin o isntituție.');
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/dashboard/profile', function () use ($app, $log) {

    $put = $app->request->put();
    $user = new User(['auth_user' => $app->auth_user]);

    $saved = $user->updateUserProfile($put);
    if ($saved) {
        ApiResult::setMessage('Datele au fost reînnoite.');
    } else {
        ApiResult::setMessage('A apărut o erroare.');
        ApiResult::setCode(0);
    }
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/dashboard/password', function () use ($app, $log) {

    $put = $app->request->put();
    $user = new User(['auth_user' => $app->auth_user]);

    $user->updateUserPassword($put);

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->get('/dashboard/institutions', function () use ($app, $log) {

    $get = $app->request->get();

    $feed = new Feed(array('auth_user' => $app->auth_user));
    if (isset($get['type']))
        $userInstitutions = $feed->getUserInstitutions($get['type']);
    else
        $userInstitutions = $feed->getUserInstitutions();

    if ($userInstitutions) {
        ApiResult::setData($userInstitutions);
    } else {
        ApiResult::setCode(0);
    }

    return ApiResult::getJSON();
});
//-----------------------------------------------------------------------------------------------------------------

$app->post('/feed', function () use ($app, $log) {

    $data = $app->request->post();

    if (ApiInput::checkFeedUrl($data)) {

        $feed = new Feed(array('auth_user' => $app->auth_user));
        $feed->addFeed($data);
    }

    return ApiResult::getJSON();
});


//-----------------------------------------------------------------------------------------------------------------

$app->put('/feed', function () use ($app, $log) {

    $data = $app->request->put();

    if (ApiInput::checkFeed($data)) {
        $feed = new Feed(array('auth_user' => $app->auth_user));
        $feed->editFeed($data);
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->get('/feeds', function () use ($app, $log) {

    $feed = new Feed();
    $feed->getFeeds();

    return ApiResult::getJSON();
});

$app->get('/backend/feeds', function () use ($app, $log) {

    $feed = new Feed();
    $feed->getBackendFeeds();

    return ApiResult::getJSON();
});
$app->get('/dashboard/article', function () use ($app, $log) {

    $get = $app->request->get();
    $data = ApiInput::asArray($get);

    if (ApiInput::checkArticleExist($data)) {
        $feed = new Feed();
        $article = $feed->getArticleById($get['article_id']);
        if ($article) {
            ApiResult::setData($article);
        } else {
            ApiResult::setCode(0);
            ApiResult::setMessage('Article Not Found');
        }
    }


    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->put('/user/feed', function () use ($app, $log) {

    $put = ApiInput::defaultValue($app->request->put(), array('institution'));

    $data = ApiInput::asArray($put);

    if (ApiInput::checkFeedInstitution($data)) {

        $feed = new User(array('auth_user' => $app->auth_user));
        $feed->updateFeed($data);
    }

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------


$app->get('/institutions/all', function () use ($app, $log) {
    $get = $app->request->get();
    $feed = new Feed();
    $feed->getInstitutionsAll($get);

    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->post('/institutions', function () use ($app, $log) {

    $post = ApiInput::defaultValue($app->request->post(), array('name', 'url'));

    if (ApiInput::checkInstitution($post)) {
        $feed = new Feed();
        $feed->addInstitution($post);
    }
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->post('/user/institutions', function () use ($app, $log) {
    $post = ApiInput::defaultValue($app->request->post(), array('name', 'url', 'type'));

    if (ApiInput::checkInstitution($post)) {
        $feed = new Feed();
        $feed->addInstitution($post);
    }
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->get('/categories', function () use ($app, $log) {

    $feed = new Feed();
    $feed->getCategories();

    return ApiResult::getJSON();
});

$app->get('/categories/last', function () use ($app, $log) {

    $feed = new Feed();
    $feed->getLastArticles();

    return ApiResult::getJSON();
});

$app->get('/about/all', function () use ($app, $log) {
    $feed = new Feed();
    $feed->getAbout();
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------
//get all testimonials
$app->get('/testimonials/all', function () use ($app, $log) {

    $feed = new Feed();
    $feed->getTestimonials();

    return ApiResult::getJSON();
});

//upload image
$app->post('/testimonials/upload', function() use($app, $log) {

    if (isset($_FILES['photo'])) {

        $file = $_FILES['photo'];
        //check if upload is image
        if (ApiInput::checkImage($file)) {

            $file_name = ApiInput::uploadTestimonialImage($file);
        }
        return ApiResult::getJSON();
    }
});

//update testimonial
$app->put('/backend/testimonials', function() use($app, $log) {
    $put = $app->request->put();
    if (isset($put['id'])) {

        if (ApiInput::checkTestimonials($put)) {
            $feed = new Feed();
            $feed->updateTestimonials($put);
        }
        return ApiResult::getJSON();
    }
});

//insert testimonial
$app->post('/backend/testimonials', function() use($app, $log) {
    $post = $app->request->post();
    if (ApiInput::checkTestimonials($post)) {
        $feed = new Feed();
        $feed->insertTestimonials($post);
    }
    return ApiResult::getJSON();
});

//delete testimonial
$app->delete('/backend/testimonials', function() use($app, $log) {
    $delete = $app->request->delete();
    $feed = new Feed();
    $feed->deleteTestimonials($delete);
});

//get description

$app->get('/backend/about-us', function () use ($app, $log) {
    $feed = new Feed();
    $feed->getAbout();
    return ApiResult::getJSON();
});


$app->post('/backend/about-us/update', function() use($app, $log) {

    $post = $app->request->post();
    $feed = new Feed();
    $feed->updateAbout($post);
    return ApiResult::getJSON();
});


/*
 * BLOG
 */

//get dashboard list
$app->get('/backend/blog', function() use($app, $log) {
    $feed = new Feed();
    $feed::getDashboardBlog();
    return ApiResult::getJSON();
});


//update blog item

$app->put('/backend/blog', function() use($app, $log) {
    $put = $app->request->put();
    if (isset($put['id'])) {

        if (ApiInput::checkBlog($put)) {
            $feed = new Feed();
            $feed->updateBlog($put);
        }
        return ApiResult::getJSON();
    }
});


//upload image
$app->post('/blog/upload', function() use($app, $log) {

    if (isset($_FILES['photo'])) {

        $file = $_FILES['photo'];
        //check if upload is image
        if (ApiInput::checkImage($file)) {

            $file_name = ApiInput::uploadBlogImage($file);
        }
        return ApiResult::getJSON();
    }
});

//insert post
$app->post('/backend/blog', function() use($app, $log) {
    $post = $app->request->post();
    if (ApiInput::checkBlog($post)) {
        $feed = new Feed();
        $feed->insertBlog($post);
    }
    return ApiResult::getJSON();
});


//delete 
$app->delete('/backend/blog', function() use($app, $log) {
    $delete = $app->request->delete();
    $feed = new Feed();
    $feed->deleteBlog($delete);
});

// get blog posts with pagination (FRONT)
$app->get('/blog/all', function() use($app, $log) {
    $get = $app->request->get();
    $feed = new Feed();
    $blog = $feed::getBlogPaginate($get['page']);
    ApiResult::setData($blog);
    return ApiResult::getJSON();
});



//get Blog post description (FRONT)


$app->get('/post', function() use($app, $log) {
    $get = $app->request->get();
    $feed = new Feed();
    $post = $feed::getPostBySlug($get['slug']);
    ApiResult::setData($post);
    return ApiResult::getJSON();
});





//-----------------------------------------------------------------------------------------------------------------

$app->put('/category', function () use ($app, $log) {
    $feed = new Feed();
    $data = $app->request->put();
    if (ApiInput::checkCategoryId($data)) {

        $feed->editCategory($data);
    }
    return ApiResult::getJSON();
});

//-----------------------------------------------------------------------------------------------------------------

$app->post('/category', function () use ($app, $log) {

    $post = $app->request->post();

    $data = ApiInput::asArray($post);
    if (ApiInput::checkCategory($post) && ApiInput::checkFeedExisting($data)) {

        $feed = new Feed();
        $feed->addCategory($post, (array_key_exists('feeds', $data)) ? $data['feeds'] : false);
    }

    return ApiResult::getJSON();
});

//-------------------------------------------------Backend------------------------------------------------------------

$app->get('/backend/dashboard', function () use ($app, $log) {

    $data = new Backend();
    $data->getDashboard();

    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->get('/backend/subscribers', function () use ($app, $log) {

    $data = new Backend();
    $data->getSubscribers(array(USER_ANDROID,USER_EMAIL));

    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->put('/backend/subscriber', function () use ($app, $log) {

    $data = $app->request->put();


    $backend = new Backend();
    $backend->editSubscriber($data);


    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->put('/backend/profile', function () use ($app, $log) {

    $data = $app->request->put();


    $backend = new Backend(['auth_user' => $app->auth_user]);
    $backend->editProfile($data);


    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->post('/backend/login', function () use ($app, $log) {

    $data = $app->request->put();

    if (ApiInput::checkUser($data)) {
        $backend = new Backend();
        $backend->login($data);
    }

    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->post('/backend/article', function () use ($app, $log) {

    $post = $app->request->post();
    $data = ApiInput::asArray($post);

    if (ApiInput::checkFeeds($data) && ApiInput::checkArticle($post)) {
        $backend = new Backend();
        $backend->createArticle($post, $data['feeds_id']);
    }

    return ApiResult::getJSON();
});
//--------------------------------------------------------------------------------------------------------------------/dashboard/login

$app->post('/dashboard/login', function () use ($app, $log) {

    $data = $app->request->put();

    if (ApiInput::checkUser($data)) {

        $backend = new User();
        $backend->login($data);
    }

    return ApiResult::getJSON();
});
//--------------------------------------------------------------------------------------------------------------------/dashboard/login
//Facebook Login or SignUp



$app->post('/dashboard/login/facebook', function () use($app, $log) {
    $data = $app->request->post();

    $backend = new User();
    $backend->loginFB($data);
    return ApiResult::getJSON();
});






$app->post('/dashboard/recovery', function () use ($app, $log) {

    $post = $app->request->post();
    $user = new User();

    if ($user->checkUserExist($post['email'])) {

        $response = $user->passwordRecover($post['email']);
        if ($response) {
            ApiResult::setMessage('Vă rugăm verificați e-mailul.');
        }
    } else {
        ApiResult::setCode(0);
        ApiResult::setMessage('Acest e-mail nu a fost înregistrat.');
    }

    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------/dashboard/login

$app->put('/dashboard/recovery/confirm', function () use ($app, $log) {

    $put = $app->request->put();
    $user = new User();

    $response = false;

    if ($put['password'] == $put['verify_password']) {
        $response = $user->changePassword($put);
    }

    if ($response) {
        //Update Email Token
        $user->updateToken($put['email_token']);
        ApiResult::setData($response);
    }


    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->get('/backend/logs', function () use ($app, $log) {

    $get = $app->request->get();

    $data = new Backend();
    $data->getLogs($get);

    return ApiResult::getJSON();
});


//---------------------------------------
//--------------------------------------------------------------------------------------------------------------------

$app->post('/user/subscribe', function () use ($app, $log) {
    $post = $app->request->post();

    if (ApiInput::checkUserSubscribe($post)) {

        $user = new User();
        $data = ApiInput::asArray($post);

        $feed = new Feed();
        $institutions = $feed->getInstitutionsAll([]);

        foreach ($institutions as $institution) {
            $data['institutions'][] = $institution['id'];
        }

        $user->subscribe($post['email'], $data['institutions'], $post['more']);
    }

    return ApiResult::getJSON();
});

//--------------------------------------------------------------------------------------------------------------------

$app->post('/user/unsubscribe', function () use ($app, $log) {

    $post = $app->request->post();

    if (ApiInput::checkEmail($post)) {

        $user = new User();
        $user->unSubscribe($post['email']);
    }

    return ApiResult::getJSON();
});

//-------------------------------------------------------------------------------------------------------------------- /backend/subscriber

$app->delete('/backend/unsubscribe/:id', function ($id) use ($app, $log) {

    //var_dump($id);
   
   // if (ApiInput::checkInt($id)) {

        $user = new Backend();
        $user->deleteSubscriber($id);
 //   }

    return ApiResult::getJSON();
});


//----------------------------------------------------------------------- /types

$app->get('/types', function () use($app, $log) {
    $feed = new Feed();
    $feed->getTypesAll();

    return ApiResult::getJSON();
});



$app->get('/debug/push', function() use($app, $log) {

    $get = $app->request->get();
    $feed = new Feed();
    //$feed->sendPushNotifications($get['key'], 'Mesaj');
     $feed->parseFeed([]);
});


//Comments

$app->get('/comments',function() use($app,$log){
   
    $feed = new Feed();
    $get = $app->request->get();
    $feed->getComments($get);
    return ApiResult::getJson();
    
});


$app->post('/comment', function() use($app, $log) {
    $post = $app->request->post();
    $feed = new Feed(array('auth_user' => $app->auth_user));
    $feed->addComment($post);
    return ApiResult::getJson();
});


$app->post('/check/captcha',function() use($app,$log){
    $post = $app->request->post();
    $feed = new Feed();
    $feed->checkCaptcha($post);
    return ApiResult::getJson();
});


$app->get('/comments/all', function() use($app, $log) {
    $feed = new Feed();
    $feed->getAllComments();
    return ApiResult::getJson();
});


$app->delete('/backend/comment', function() use($app, $log) {
    $feed = new Feed();
    $comment = $app->request->post();
    $feed->removeComment($comment);
    return ApiResult::getJson();
});
