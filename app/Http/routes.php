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

$app->get('/', [
	'as' => 'home',
	'uses' => 'HomeController@index'
]);

$app->post('/post', [
	'as' => 'post',
	'uses' => 'HomeController@post'
]);

$app->get('version', function () use ($app) {
    return $app->version();
});

