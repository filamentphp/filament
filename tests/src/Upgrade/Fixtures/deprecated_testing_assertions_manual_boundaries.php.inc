<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionBoundaries;

use Closure;
use Filament\Forms\Testing\TestsForms;
use Livewire\Features\SupportTesting\Testable;

class CustomAssertions
{
    public function assertFormFieldIsDisabled(string $key): static
    {
        return $this;
    }

    public function assertActionHeld(string $actions): static
    {
        return $this;
    }
}

class CustomTestable extends Testable
{
    public function assertActionHeld(string $actions): string
    {
        return 'custom';
    }
}

class CustomCanonicalTestable extends Testable
{
    public function assertActionHalted(string $actions): string
    {
        return 'custom';
    }
}

class CustomDispatchTestable extends Testable
{
    public function __call($method, $params)
    {
        return 'custom';
    }
}

class InheritedCustomDispatchTestable extends CustomDispatchTestable {}

trait CustomFactory
{
    protected function assertActionHalted(): string
    {
        return 'custom';
    }
}

class TraitTestable extends Testable
{
    use CustomFactory;
}

class CustomForms extends TestsForms
{
    public function assertFormFieldVisible(): Closure
    {
        return fn (): string => 'custom';
    }
}

function manual(CustomAssertions $custom, CustomTestable $legacyOverride, CustomCanonicalTestable $canonicalOverride, InheritedCustomDispatchTestable $dispatchOverride, TraitTestable $traitOverride, CustomForms $forms, Testable $testable, object $unknown, $mixed, string $method): void
{
    $custom->assertFormFieldIsDisabled('title')->assertActionHeld('save');
    $legacyOverride->assertActionHeld('save');
    $legacyOverride->set('title', 'Hello')->assertActionHeld('save');
    $canonicalOverride->assertActionHeld('save');
    $dispatchOverride->assertActionHeld('save');
    $dispatchOverride->set('title', 'Hello')->assertActionHeld('save');
    $traitOverride->assertActionHeld('save');
    $forms->assertFormFieldIsVisible();
    $forms->customFactory()->assertFormFieldIsVisible();
    $unknown->assertFormFieldIsDisabled('title');
    $mixed->assertTableBulkActionHeld('delete');
    $testable->{$method}('title');
    $callback = [$testable, 'assertActionHeld'];
    $testable->getFormComponent('title')->assertActionHeld('save');
    $testable->customMacro()->assertFormFieldIsDisabled('title');
    $testable->get('property')->assertFormFieldIsDisabled('title');
    $stored = $testable->getFormComponent('title');
    $stored->assertActionHeld('save');
    // Macro lookup is case-sensitive; this is not the deprecated Filament macro.
    $testable->ASSERTACTIONHELD('save');
    $testable->ASSERTTABLEACTIONSEXISTINORDER(['edit']);
    $custom->assertTableActionsExistInOrder(['edit']);
    $legacyOverride->assertTableActionsExistInOrder(['edit']);
    $unknown->assertTableActionsExistInOrder(['edit']);
    $testable->customMacro()->assertTableActionsExistInOrder(['edit']);
    $callback = [$testable, 'assertTableActionsExistInOrder'];
}
