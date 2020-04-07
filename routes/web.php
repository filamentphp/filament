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

// Asset routes...
Route::name('assets.')->group(function () {

    Route::get('filament.css', 'AssetController@css')->name('css');
    Route::get('filament.js', 'AssetController@js')->name('js');
    
});

// Authentication routes...
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

// Authenticated routes...
Route::name('admin.')->middleware('auth.filament')->group(function () {

    Route::get('/', 'DashboardController')->name('dashboard');

    Route::resource('users', 'UserController')->only([
        'index', 
        'edit',
    ]);

    Route::resource('roles', 'RoleController')->only([
        'index', 
        //'edit',
    ]);

    Route::resource('permissions', 'PermissionController')->only([
        'index', 
        //'edit',
    ]);

    Route::post('file-upload', function () {
        return call_user_func(request()->input('component').'::fileUpload');
    })->name('file-upload');

});

// Images
Route::get('/image/{path}', 'ImageController')->where('path', '.*')->name('image');
