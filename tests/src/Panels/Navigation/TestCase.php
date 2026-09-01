<?php

namespace Filament\Tests\Panels\Navigation;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }
}
