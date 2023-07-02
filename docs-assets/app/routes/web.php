<?php

use App\Http\Livewire\ActionsDemo;
use App\Http\Livewire\Forms\FieldsDemo;
use App\Http\Livewire\Forms\GettingStartedDemo;
use App\Http\Livewire\Forms\LayoutDemo as FormsLayoutDemo;
use App\Http\Livewire\Infolists\EntriesDemo;
use App\Http\Livewire\Infolists\LayoutDemo as InfolistsLayoutDemo;
use App\Http\Livewire\NotificationsDemo;
use App\Http\Livewire\TablesDemo;
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
Route::get('/forms/layout', FormsLayoutDemo::class);
Route::get('/infolists/entries', EntriesDemo::class);
Route::get('/infolists/layout', InfolistsLayoutDemo::class);
Route::get('/notifications', NotificationsDemo::class);
Route::get('/tables', TablesDemo::class);
