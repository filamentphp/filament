<?php

use App\Livewire\ActionsDemo;
use App\Livewire\Forms\FieldsDemo;
use App\Livewire\Forms\FieldsOverview;
use App\Livewire\Infolists\EntriesDemo;
use App\Livewire\Infolists\EntriesOverview;
use App\Livewire\NotificationsDemo;
use App\Livewire\Panels\Navigation\ActiveIcon;
use App\Livewire\Panels\Navigation\Badge;
use App\Livewire\Panels\Navigation\BadgeColor;
use App\Livewire\Panels\Navigation\BadgeTooltip;
use App\Livewire\Panels\Navigation\ChangeIcon;
use App\Livewire\Panels\Navigation\CustomItems;
use App\Livewire\Panels\Navigation\DisabledNavigation;
use App\Livewire\Panels\Navigation\Group;
use App\Livewire\Panels\Navigation\GroupCollapsible;
use App\Livewire\Panels\Navigation\GroupNotCollapsible;
use App\Livewire\Panels\Navigation\SidebarCollapsibleOnDesktop;
use App\Livewire\Panels\Navigation\SidebarFullyCollapsibleOnDesktop;
use App\Livewire\Panels\Navigation\SortItems;
use App\Livewire\Panels\Navigation\TopNavigation;
use App\Livewire\Panels\Navigation\UserMenuCustomization;
use App\Livewire\PrimesDemo;
use App\Livewire\Schemas\LayoutDemo;
use App\Livewire\Schemas\OverviewDemo;
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

Route::prefix('forms')->group(function (): void {
    Route::get('overview', FieldsOverview::class);
    Route::get('fields', FieldsDemo::class);
});

Route::prefix('infolists')->group(function (): void {
    Route::get('overview', EntriesOverview::class);
    Route::get('entries', EntriesDemo::class);
});

Route::get('primes', PrimesDemo::class);

Route::get('/notifications', NotificationsDemo::class);

Route::prefix('panels')->middleware(['panel:admin'])->group(function (): void {
    Route::prefix('navigation')->group(function (): void {
        Route::get('user-menu-customization', UserMenuCustomization::class);
        Route::get('disabled-navigation', DisabledNavigation::class);
        Route::get('active-icon', ActiveIcon::class);
        Route::get('change-icon', ChangeIcon::class);
        Route::get('sort-items', SortItems::class);
        Route::get('custom-items', CustomItems::class);
        Route::get('top-navigation', TopNavigation::class);
        Route::get('sidebar-collapsible-on-desktop', SidebarCollapsibleOnDesktop::class);
        Route::get('sidebar-fully-collapsible-on-desktop', SidebarFullyCollapsibleOnDesktop::class);
        Route::get('badge', Badge::class);
        Route::get('badge-color', BadgeColor::class);
        Route::get('badge-tooltip', BadgeTooltip::class);
        Route::get('group', Group::class);
        Route::get('group-collapsible', GroupCollapsible::class);
        Route::get('group-not-collapsible', GroupNotCollapsible::class);
    });
});

Route::prefix('schemas')->group(function (): void {
    Route::get('layout', LayoutDemo::class);
    Route::get('overview', OverviewDemo::class);
});

Route::get('/tables', TablesDemo::class);
