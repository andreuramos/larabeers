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

Route::get('/', function () {
    return view('front');
});

Route::get('/dashboard', "DashboardController@index");
Route::post('/dashboard/upload-csv', "DashboardController@upload_csv");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
