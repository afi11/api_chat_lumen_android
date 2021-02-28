<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api','middleware' => 'CorsMiddleware'], function() use($router){

    // Auth
    $router->post('register','UserController@register');
    $router->post('login','UserController@login');
    $router->post('update_profil','HomeController@update');
    $router->post('logout','HomeController@logout');

    // Update online / offline user
    $router->post('updateuseronline','HomeController@updateUserOnline');
    $router->post('updateuserclose','HomeController@upUserClose');
    $router->get('getuserbyid/{id}','HomeController@getUserById');

    // Home
    $router->get('home','HomeController@index');
    $router->post('letmessage','HomeController@letMessageWithPeople');
    $router->get('latestmessage','HomeController@latestMessage');
    $router->get('countunreadmessage/{sender}/{receiver}','HomeController@countUnreadMessage');
    $router->get('getlatetsmessage/{sender}/{receiver}','HomeController@getLatestMessage');

    // Chats
    $router->get('getchat/{receiver}','ChatController@getChat');
    $router->post('sendchat','ChatController@sendMessage');
    $router->get('readmessage/{receiver}','ChatController@readMessage');

});
