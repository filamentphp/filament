<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionBoundaries;

use Filament\Actions\Testing\TestsActions;
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

function manual(CustomAssertions $custom, TestsActions $mixin, Testable $testable, object $unknown, string $method): void
{
    $custom->assertFormFieldIsDisabled('title')->assertActionHeld('save');
    $custom->assertTableActionsExistInOrder(['edit']);
    $unknown->assertFormFieldIsDisabled('title');
    $unknown->setActionData([]);
    $unknown->assertActionDataSet(data: []);
    $mixin->setActionData();
    $mixin->assertActionDataSet();
    $testable->{$method}('title');
    $callback = [$testable, 'assertActionHeld'];
    $testable->get('property')->assertFormFieldIsDisabled('title');
    $testable->get('property')->setActionData([]);
    // Macro lookup is case-sensitive.
    $testable->ASSERTACTIONHELD('save');
}
