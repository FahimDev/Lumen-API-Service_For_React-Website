<?php

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

//$router->get('/test','serviceController@MemberPrivateKey');


$router->post('/authority',['middleware'=>'auth','uses'=>'serviceController@Signin']); //http://localhost:8000/authority?userName=user01213&password=1234
$router->post('/update-password',['middleware'=>'auth','uses'=>'serviceController@updatePassword']);
$router->post('/update-profile',['middleware'=>'auth','uses'=>'serviceController@updateProfile']);
$router->post('/update-profile-img',['middleware'=>'auth','uses'=>'serviceController@uploadImg']);


$router->get('/services',['middleware'=>'auth','uses'=>'webManager@selectServices']);
$router->get('/research',['middleware'=>'auth','uses'=>'webManager@selectResearch']);
$router->get('/contact',['middleware'=>'auth','uses'=>'webManager@selectContact']);
$router->get('/about',['middleware'=>'auth','uses'=>'webManager@selectAbout']);
$router->get('/title',['middleware'=>'auth','uses'=>'webManager@selectWebTitles']); //http://localhost:8000/title
$router->post('/title',['middleware'=>'auth','uses'=>'webManager@selectWebTitle']); //http://localhost:8000/title?id=1

$router->get('/members',['middleware'=>'auth','uses'=>'webManager@selectMembers']);
$router->get('/member-profile/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberInfo']); //http://localhost:8000/profile/fahim0373
$router->get('/member-academic/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberEducation']);
$router->get('/member-work/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberWorkHistory']);
$router->get('/member-url/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberURL']);
$router->get('/member-hashTag/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberHashTag']);
$router->get('/member-hobby/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberHobby']);
$router->get('/member-experties/{memberID}/{type}',['middleware'=>'auth','uses'=>'webManager@selectMemberExp']);
$router->get('/member-earn/{memberID}/{type}',['middleware'=>'auth','uses'=>'webManager@selectMemberEarn']);
$router->get('/member-network/{memberID}',['middleware'=>'auth','uses'=>'webManager@selectMemberNetwork']);
