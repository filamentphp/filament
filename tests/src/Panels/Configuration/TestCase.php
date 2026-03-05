<?php

namespace Filament\Tests\Panels\Configuration;

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('configuration'));

        $this->actingAs(User::factory()->create());
    }
}
