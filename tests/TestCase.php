<?php

namespace Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Filament;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\LivewireServiceProvider;
use Thomaswelton\LaravelGravatar\LaravelGravatarServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            TablesServiceProvider::class,
            LivewireServiceProvider::class,
            LaravelGravatarServiceProvider::class,
        ];
    }
}
