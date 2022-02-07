<?php

use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Foundation\Application;

$app = (new Application())
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(LivewireServiceProvider::class);
$app->register(FormsServiceProvider::class);
$app->register(TablesServiceProvider::class);
$app->register(FilamentServiceProvider::class);

return $app;
