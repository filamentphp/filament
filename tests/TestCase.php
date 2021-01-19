<?php

namespace Filament\Tests;

use Filament\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase, WithFaker;

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
            \Filament\FilamentServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
    }
}
