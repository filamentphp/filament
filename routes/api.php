<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authenticated routes...
Route::middleware('auth.filament:api')->group(function () {

    // curl -H "Accept: application/json" -X GET http://127.0.0.1:8000/filament/api/user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
