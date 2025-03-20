<?php

namespace Filament\Tests\Commands;

use Filament\Tests\TestCase;

use function Pest\Laravel\artisan;

uses(TestCase::class);

test('can create a table widget in livewire directory', function () {
    $widgetPath = $this->app->basePath('app/Livewire/TestWidget.php');

    if (file_exists($widgetPath)) {
        unlink($widgetPath);
    }

    expect(file_exists($widgetPath))->toBeFalse();

    artisan('make:filament-widget', [
        'name' => 'TestWidget',
        '--force' => true,
    ])
        ->expectsQuestion('What type of widget do you want to create?', 'Table')
        ->expectsQuestion('What is the resource you would like to create this in?', '')
        ->expectsQuestion('Where would you like to create this?', 'App\\Livewire alongside other Livewire components')
        ->assertSuccessful();

    expect(file_exists($widgetPath))->toBeTrue();
});
