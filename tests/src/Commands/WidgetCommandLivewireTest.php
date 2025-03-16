<?php

namespace Filament\Tests\Commands;

use Filament\Tests\TestCase;
use function Pest\Laravel\artisan;
use Illuminate\Support\Facades\File;

uses(TestCase::class)
    ->group('integration')
    ->beforeEach(function () {
        File::deleteDirectory($this->app->basePath('app/Livewire'));
    });

it('can create a table widget in livewire directory', function () {
    artisan('make:filament-widget', [
        'name' => 'TestWidget',
        '--force' => true,
        '--resource' => 'InvoiceResource',
    ])
        ->expectsQuestion('What type of widget do you want to create?', 'Table')
        ->expectsQuestion('Where would you like to create this?', 'App\\Livewire alongside other Livewire components')
        ->assertSuccessful();

    $widgetPath = $this->app->basePath('app/Livewire/TestWidget.php');
    expect(file_exists($widgetPath))->toBeTrue();
});