<?php

use Illuminate\Support\Facades\Route;

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
   return redirect()->route('home');
})->middleware('auth');;

Auth::routes();

Route::get('/store/everything', 'SlackController@storeEverything')->middleware('auth');
Route::get('/store/users', 'SlackController@storeUsers')->middleware('auth');
Route::get('/store/channels/main', 'SlackController@storeMainChannels')->middleware('auth');
Route::get('/store/channels/private', 'SlackController@storePrivateChannels')->middleware('auth');
Route::get('/store/conversations/all', 'SlackController@storeAllConversations')->middleware('auth');
Route::get('/store/conversations/{channel}', 'SlackController@storeConversations')->name("backup.channel")->middleware('auth');
Route::get('/channels', 'HomeController@index')->name('home');
Route::get('/channels/{channel}', 'HomeController@channel')->name('channel');
Route::get('login/slack', 'Auth\LoginController@redirectToProvider')->name('login.slack');
Route::get('login/slack/callback', 'Auth\LoginController@handleProviderCallback');
