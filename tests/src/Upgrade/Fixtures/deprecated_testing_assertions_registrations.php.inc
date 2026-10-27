<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionRegistrations;

use Livewire\Features\SupportTesting\Testable;

Testable::macro('assertActionHeld', fn (): string => 'custom');

function customMacros(Testable $testable): void
{
    $testable->assertActionHeld('save');
    $testable->callAction('save')->assertActionHeld('save');
}
