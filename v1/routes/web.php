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

$router->get('/services',['middleware'=>'auth','uses'=>'webManager@selectServices']);
$router->get('/research',['middleware'=>'auth','uses'=>'webManager@selectResearch']);
$router->get('/contact',['middleware'=>'auth','uses'=>'webManager@selectContact']);
$router->get('/about',['middleware'=>'auth','uses'=>'webManager@selectAbout']);
$router->get('/title',['middleware'=>'auth','uses'=>'webManager@selectWebTitles']); //http://localhost:8000/title
$router->post('/title',['middleware'=>'auth','uses'=>'webManager@selectWebTitle']); //http://localhost:8000/title?page=Glitch Studios
