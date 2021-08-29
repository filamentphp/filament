<?php

namespace Tests;

use Filament\Forms\FormsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FormsServiceProvider::class,
        ];
    }
}
