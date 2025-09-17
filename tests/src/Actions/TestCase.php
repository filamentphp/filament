<?php

namespace Filament\Tests\Actions;

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
