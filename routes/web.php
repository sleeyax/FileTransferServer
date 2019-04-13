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
Route::get('/test/upload', function() {
    return view('tests/upload');
});
Route::post('/test', 'TestController@test');
Route::get('/file/info/{key}', 'FileController@info');
Route::get('/file/verify/{key}', 'FileController@verify');
Route::get('/file/download/{key}', 'FileController@download');
Route::post('/file/upload', 'FileController@upload');
