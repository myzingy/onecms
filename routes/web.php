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
    return redirect()->guest('admin');
    //return view('welcome');
});
//Route::get('/vking', 'VkingController@index')->name('vking');
Route::resource('/vking', VkingController::class);
