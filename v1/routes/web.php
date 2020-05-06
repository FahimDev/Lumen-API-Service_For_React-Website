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

$router->get('/services','webManager@selectServices');
$router->get('/research','webManager@selectResearch');
$router->get('/contact','webManager@selectContact');
$router->get('/about','webManager@selectAbout');
$router->get('/title','webManager@selectWebTitles'); //http://localhost:8000/title
$router->post('/title','webManager@selectWebTitle'); //http://localhost:8000/title?page=Glitch Studios
