<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionCanonicalRegistration;

use Livewire\Features\SupportTesting\Testable;

Testable::macro('assertActionHalted', fn (): string => 'custom');

function customCanonicalMacro(Testable $testable): void
{
    $testable->assertActionHeld('save');
    $testable->callAction('save')->assertActionHeld('save');
}
