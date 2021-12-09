<?php

namespace Filament\Tests\Admin\Pages;

use Filament\Tests\Models\User;
use Filament\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [
            PagesServiceProvider::class,
        ]);
    }
}
