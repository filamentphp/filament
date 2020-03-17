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

// asset routes...
Route::name('assets.')->group(function () {

    Route::get('alpine.css', 'AssetController@css')->name('css');
    Route::get('alpine.js', 'AssetController@js')->name('js');
    
});

// authentication routes...
Route::name('auth.')->namespace('Auth')->group(function () {

    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name('logout');

    Route::prefix('password')->name('password.')->group(function () {
        Route::get('forgot', 'ForgotPasswordController@showLinkRequestForm')->name('forgot');
        Route::post('forgot', 'ForgotPasswordController@sendResetLinkEmail')->name('email');
        Route::get('reset/{token}', 'ResetPasswordController@showResetForm')->name('reset');
        Route::post('reset', 'ResetPasswordController@reset')->name('update');        
    });
    
});

// protected admin routes...
Route::name('admin.')->middleware('auth.alpine')->group(function () {

    Route::get('/', 'DashboardController')->name('dashboard');

    Route::resource('users', 'UserController')->only([
        'index', 
        'edit',
    ]);

});