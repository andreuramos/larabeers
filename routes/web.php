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

use App\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', "HomeController@home");
Route::post('/find', "HomeController@find");
Route::group(['prefix' => '/ajax'], function(){
    Route::get('search', "HomeController@ajax_search");
    Route::get('brewer_autocomplete', "HomeController@ajax_brewer_autocomplete");
});
Route::group(['prefix' => '/beer'], function() {
    Route::get('/{id}', "HomeController@show_beer");
    Route::get('/{id}/edit', "DashboardController@edit_beer");
    Route::post('/{id}/update', "DashboardController@update_beer");
    Route::post('/{id}/new_label', "DashboardController@add_label_to_beer");
});
Route::get('/brewer/{id}', "HomeController@show_brewer");

Route::group(['prefix' => '/stats'], function(){
    Route::get('countries', "HomeController@list_countries");
});

Route::post('label/{id}/edit', "DashboardController@update_label");


Route::group(['prefix' => '/dashboard'], function () {
    Route::get('/', "DashboardController@index");
    Route::post('/upload-csv', "DashboardController@upload_csv");
    Route::get('/settings', "DashboardController@settings");
    Route::get('/settings/google_auth_comeback', 'DashboardController@google_auth_comeback');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
