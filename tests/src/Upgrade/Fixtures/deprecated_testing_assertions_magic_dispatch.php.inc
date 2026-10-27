<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionMagicDispatch;

use Livewire\Features\SupportTesting\Testable;

function customMacroResult(Testable $testable): void
{
    $testable->__call('customMacro', [])->assertActionHeld('save');
    $stored = $testable->__call('customMacro', []);
    $stored->assertFormFieldIsDisabled('title');
}
