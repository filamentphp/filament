<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionMixinRegistration;

use Closure;
use Livewire\Features\SupportTesting\Testable;

class CustomAssertions
{
    public function assertActionHalted(): Closure
    {
        return fn (): string => 'custom';
    }
}

Testable::mixin(new CustomAssertions);

function customMixin(Testable $testable): void
{
    $testable->assertActionHeld('save');
    $testable->callAction('save')->assertActionHeld('save');
}
