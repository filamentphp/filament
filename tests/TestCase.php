<?php

namespace Filament\Tests;

use Filament\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['auth']->shouldUse('filament');
    }

    protected function getPackageAliases($app)
    {
        return [
            'FilamentManager' => Filament::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            \BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
            \BladeUI\Icons\BladeIconsServiceProvider::class,
            \Filament\FilamentServiceProvider::class,
            \Filament\FormsServiceProvider::class,
            \Filament\TablesServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,
            \Thomaswelton\LaravelGravatar\LaravelGravatarServiceProvider::class,
            \Watson\Active\ActiveServiceProvider::class,
        ];
    }
}
