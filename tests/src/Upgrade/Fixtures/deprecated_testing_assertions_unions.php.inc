<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionUnions;

use Closure;
use Filament\Forms\Testing\TestsForms;
use Livewire\Features\SupportTesting\Testable;

class OverridingTestable extends Testable
{
    public function assertActionHalted(): string
    {
        return 'custom';
    }
}

class OverridingForms extends TestsForms
{
    public function assertFormFieldVisible(): Closure
    {
        return fn (): string => 'custom';
    }
}

class StoredReceivers
{
    public Testable | OverridingTestable $testable;

    public TestsForms | OverridingForms $forms;

    public function assertions(): void
    {
        $this->testable->assertActionHeld('save');
        $this->forms->assertFormFieldIsVisible();
    }
}

function normalized(Testable | OverridingTestable $testable, TestsForms | OverridingForms $forms): void
{
    $testable->assertActionHeld('save');
    $forms->assertFormFieldIsVisible();
}
