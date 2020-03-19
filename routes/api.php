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

// protected routes...
Route::middleware('auth.filament:api')->group(function () {

    // curl -H "Accept: application/json" -X GET http://localhost:8000/cp/api/user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
