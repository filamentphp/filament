<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\NavDemoPanelProvider;
use App\Providers\Filament\TenancyDemoPanelProvider;
use App\Providers\Filament\TenantMenuGroupingDemoPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    NavDemoPanelProvider::class,
    TenancyDemoPanelProvider::class,
    TenantMenuGroupingDemoPanelProvider::class,
];
