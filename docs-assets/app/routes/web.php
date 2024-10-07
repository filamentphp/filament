<?php

use App\Livewire\ActionsDemo;
use App\Livewire\Forms\FieldsDemo;
use App\Livewire\Forms\GettingStartedDemo;
use App\Livewire\Infolists\EntriesDemo;
use App\Livewire\NotificationsDemo;
use App\Livewire\Schema\LayoutDemo;
use App\Livewire\TablesDemo;
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

Route::get('/actions', ActionsDemo::class);
Route::get('/forms/fields', FieldsDemo::class);
Route::get('/forms/getting-started', GettingStartedDemo::class);
Route::get('/infolists/entries', EntriesDemo::class);
Route::get('/schema/layout', LayoutDemo::class);
Route::get('/notifications', NotificationsDemo::class);
Route::get('/tables', TablesDemo::class);
