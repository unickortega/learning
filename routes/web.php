<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/user/create/post',['as'=>'json_create_post','uses'=>'PostController@json_create_post']);
Route::post('/user/update/post',['as'=>'json_update_post','uses'=>'PostController@json_update_post']);
Route::post('/user/delete/post',['as'=>'json_delete_post','uses'=>'PostController@json_delete_post']);
Route::get('/user/get/post',['as'=>'json_get_post','uses'=>'PostController@json_get_post']);
Route::post('/user/update/like',['as'=>'json_update_like','uses'=>'PostController@json_update_like']);
