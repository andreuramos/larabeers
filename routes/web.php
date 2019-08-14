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

Route::get('/dashboard', "DashboardController@index");
Route::post('/dashboard/upload-csv', "DashboardController@upload_csv");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
