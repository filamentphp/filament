<?php

use App\Http\Livewire\Actions;
use App\Http\Livewire\Forms\Fields;
use App\Http\Livewire\Forms\Layout;
use App\Http\Livewire\Infolists\Entries;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/actions', Actions::class);
Route::get('/forms/fields', Fields::class);
Route::get('/forms/layout', Layout::class);
Route::get('/infolists/entries', Entries::class);
Route::get('/infolists/layout', Layout::class);
