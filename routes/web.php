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

Route::get('/create_admin', function () {
    $admin_user = User::first();

    if (!$admin_user) {
        $admin_user = new User(['name' => 'admin', 'email'=> 'andreu.ramos.amengual@gmail.com', 'password' => Hash::make('123')]);
        $admin_user->save();
    }

    return $admin_user->email;
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
