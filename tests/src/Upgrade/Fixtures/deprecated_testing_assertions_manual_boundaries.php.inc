<?php

namespace Filament\Tests\Upgrade\Fixtures\TestingAssertionBoundaries;

use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsFormComponentActions;
use Filament\Infolists\Testing\TestsInfolistActions;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
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

    public function assertHasNoFormComponentActionErrors(array $keys = []): static
    {
        return $this;
    }
}

class CustomTestable extends Testable
{
    public function setTableActionData(array $data): static
    {
        return $this;
    }

    public function assertHasInfolistActionErrors(array $keys = []): static
    {
        return $this;
    }
}

class RedirectingTestable extends Testable
{
    public function mountAction(string $action): CustomAssertions
    {
        return new CustomAssertions;
    }
}

function nativeChains(CustomTestable $custom, RedirectingTestable $redirecting): void
{
    $custom->mountAction('edit')->setTableActionData(data: []);
    $custom->mountTableAction('edit')->assertTableActionMounted('edit')->setTableActionData(data: []);
    $custom->assertActionExists('edit')->assertHasInfolistActionErrors(keys: []);
    $redirecting->mountAction('edit')->assertHasNoFormComponentActionErrors();
}

function mixinFactories(TestsFormComponentActions $forms, TestsInfolistActions $infolists, TestsTableActions $tables, TestsBulkActions $bulkActions, CustomTestable $custom): void
{
    $forms->setFormComponentActionData();
    $infolists->assertHasInfolistActionErrors();
    $tables->assertTableActionDataSet();
    $bulkActions->assertHasNoTableBulkActionErrors();
    $custom->setTableActionData(data: []);
    $custom->assertHasInfolistActionErrors(keys: []);
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
    $unknown->setFormComponentActionData(data: []);
    $unknown->assertHasNoTableActionErrors();
    $testable->{$method}('title');
    $callback = [$testable, 'assertActionHeld'];
    $testable->get('property')->assertFormFieldIsDisabled('title');
    $testable->get('property')->setActionData([]);
    // Macro lookup is case-sensitive.
    $testable->ASSERTACTIONHELD('save');
}
