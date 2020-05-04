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

Route::group(['prefix' => '/api'], function() {
    Route::get('random', 'ApiController@randomBeers');
});

Route::get('/', "HomeController@home");
Route::post('/find', "HomeController@find");
Route::group(['prefix' => '/ajax'], function(){
    Route::get('search', "HomeController@ajax_search");
    Route::get('brewer_autocomplete', "HomeController@ajax_brewer_autocomplete");
    Route::get('style_autocomplete', "HomeController@ajax_style_autocomplete");
    Route::get('tag_autocomplete', "HomeController@ajax_tag_autocomplete");
    Route::post('create_brewer', "BrewerController@ajax_create_brewer");
});
Route::group(['prefix' => '/beer'], function() {
    Route::get('/new', "BeerController@new_beer");
    Route::post('/create', "BeerController@create_beer");
    Route::get('/{id}', "BeerController@show_beer");
    Route::get('/{id}/edit', "BeerController@edit_beer");
    Route::post('/{id}/update', "BeerController@update_beer");
    Route::post('/{id}/new_label', "BeerController@add_label_to_beer");
});
Route::group(['prefix' => '/brewer'], function() {
    Route::get('/new', "BrewerController@new_brewer");
    Route::post('/create', 'BrewerController@create_brewer');
    Route::get('/{id}', "BrewerController@show_brewer");
    Route::get('/{id}/edit', "BrewerController@edit_brewer");
    Route::post('/{id}/update', 'BrewerController@update_brewer');
});


Route::group(['prefix' => '/stats'], function(){
    Route::get('countries', "HomeController@list_countries");
});

// TODO: change url to /beer/{id}/label/{id}/edit and move to beer group
Route::post('label/{id}/edit', "BeerController@update_label");


Route::group(['prefix' => '/dashboard'], function () {
    Route::get('/', "DashboardController@index");
    Route::post('/upload-csv', "DashboardController@upload_csv");
    Route::get('/settings', "DashboardController@settings");
    Route::get('/settings/google_auth_comeback', 'DashboardController@google_auth_comeback');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
