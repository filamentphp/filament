<?php

namespace Filament\Tests\Admin\GlobalSearch;

use Filament\Tests\Models\User;
use Filament\Tests\TestCase as BaseTestCase;

class LimitedTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [
            LimitedGlobalSearchServiceProvider::class,
        ]);
    }
}
