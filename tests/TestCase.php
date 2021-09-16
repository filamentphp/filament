<?php

namespace Tests;

use Filament\Forms\FormsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }
}
