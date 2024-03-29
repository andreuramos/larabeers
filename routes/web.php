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

Route::group(['prefix' => '/api', 'middleware' => ['cors']], function () {
    Route::get('random', 'ApiController@randomBeers');
    Route::get('search', 'ApiController@searchBeers');
    Route::get('find-by-id', 'ApiController@findBeersById');
    Route::get('count-by-year', 'ApiController@countBeersByYear');
});

Route::get('/', "HomeController@home");
Route::post('/find', "HomeController@find");
Route::group(['prefix' => '/ajax'], function () {
    Route::get('search', "HomeController@ajaxSearch");
    Route::get('brewer_autocomplete', "HomeController@ajaxBrewerAutocomplete");
    Route::get('style_autocomplete', "HomeController@ajaxStyleAutocomplete");
    Route::get('tag_autocomplete', "HomeController@ajaxTagAutocomplete");
    Route::post('create_brewer', "BrewerController@ajaxCreateBrewer");
    Route::get('geocode', "DashboardController@ajaxGeocode");
});
Route::group(['prefix' => '/beer'], function () {
    Route::get('/new', "BeerController@newBeer");
    Route::post('/create', "BeerController@createBeer");
    Route::get('/{id}', "BeerController@showBeer");
    Route::get('/{id}/delete', "BeerController@deleteBeer");
    Route::get('/{id}/edit', "BeerController@editBeer");
    Route::post('/{id}/update', "BeerController@updateBeer");
    Route::post('/{id}/new_label', "BeerController@addLabelToBeer");
    Route::get('{beer_id}/label/{label_id}/delete', "BeerController@deleteLabelFromBeer");
});
Route::group(['prefix' => '/brewer'], function () {
    Route::get('/new', "BrewerController@newBrewer");
    Route::post('/create', 'BrewerController@createBrewer');
    Route::get('/{id}', "BrewerController@showBrewer");
    Route::get('/{id}/edit', "BrewerController@editBrewer");
    Route::post('/{id}/update', 'BrewerController@updateBrewer');
});
Route::group(['prefix' => '/tag'], function () {
    Route::get('{id}', 'TagController@showTag');
});


Route::group(['prefix' => '/stats'], function () {
    Route::get('countries', "HomeController@listCountries");
    Route::get('years', "HomeController@listYears");
});

// TODO: change url to /beer/{id}/label/{id}/edit and move to beer group
Route::post('label/{id}/edit', "BeerController@updateLabel");


Route::group(['prefix' => '/dashboard'], function () {
    Route::get('/', "DashboardController@index");
    Route::post('/upload-csv', "DashboardController@uploadCsv");
    Route::get('/settings', "DashboardController@settings");
    Route::get('/settings/google_auth_comeback', 'DashboardController@googleAuthComeback');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
