<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedTestingAssertions;

use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Filament\Tests\livewire;

function assertions(Testable $testable, ?Testable $nullable, array $arguments): void
{
    $testable->setActionData(data: ['title' => 'Changed'])->assertActionDataSet(data: ['title' => 'Changed'])->assertHasNoActionErrors();
    $testable->assertHasActionErrors(keys: ['title' => 'required']);
    $nullable?->setActionData([])->assertActionDataSet(data: []);
    $callback = $testable->assertActionDataSet(...);
    $testable->assertFormFieldIsDisabled(key: 'title', form: 'editForm');
    $testable->assertFormFieldIsEnabled('title');
    $testable->assertFormFieldIsReadOnly('slug');
    $testable->assertFormFieldIsHidden('secret');
    $testable->assertFormFieldIsVisible('title');
    $testable->assertActionHeld(actions: 'save');
    $testable->assertActionHeld();
    $testable->assertTableActionHeld(actions: ['edit']);
    $testable->assertTableBulkActionHeld(actions: 'delete');
    $testable->assertTableActionsExistInOrder(names: ['edit', 'delete']);
    $testable->assertTableActionsExistInOrder(...$arguments);
    $nullable?->assertTableActionsExistInOrder(['edit', 'delete']);
    $callback = $testable->assertTableActionsExistInOrder(...);
    $testable->assertTableActionsExistInOrder(['edit'])->assertTableActionsExistInOrder(['delete']);
    $testable->assertTableRecordActionsExistInOrder(['edit'])->assertTableActionsExistInOrder(['delete']);
    Livewire::test('edit-post')->assertTableActionsExistInOrder(['edit', 'delete']);
    $nullable?->assertFormFieldIsVisible(key: 'title', form: null);
    $nullable?->assertFormFieldIsVisible('title')?->assertFormFieldIsEnabled('title');
    $testable->assertFormFieldIsDisabled(...$arguments);
    $callback = $testable->assertActionHeld(...);
    $callback = $testable->assertFormFieldIsVisible(...);
    $testable->assertFormFieldIsVisible('title')->assertFormFieldIsEnabled('title')->assertActionHeld('save');
    $testable->assertFormFieldVisible('title')->assertFormFieldIsEnabled('title');
    $testable->set('title', 'Hello')->assertFormFieldIsVisible('title');
    $testable->assertDispatched('saved')->assertActionHeld('save');
    $testable->fillForm(['title' => 'Hello'])->assertFormFieldIsEnabled('title');
    $testable->mountAction('save')->assertActionHeld('save');
    $testable->callAction('save')->assertActionHeld('save');
    $testable->mountTableAction('edit', 1)->assertTableActionHeld('edit');
    $testable->callTableAction('edit', 1)->assertTableActionHeld('edit');
    $testable->mountTableBulkAction('delete', [1])->assertTableBulkActionHeld('delete');
    $testable->callTableBulkAction('delete', [1])->assertTableBulkActionHeld('delete');
    $testable->callFormComponentAction('title', 'save')->assertHasFormComponentActionErrors();
    $testable->mountFormComponentAction('title', 'save')->setFormComponentActionData(data: [])->assertFormComponentActionDataSet(data: [])->assertHasNoFormComponentActionErrors();
    $testable->callInfolistAction('title', 'save')->assertHasInfolistActionErrors();
    $testable->mountInfolistAction('title', 'save')->setInfolistActionData(data: [])->assertInfolistActionDataSet(data: [])->assertHasNoInfolistActionErrors();
    $testable->callTableAction('edit', 1)->assertHasTableActionErrors();
    $testable->mountTableAction('edit', 1)->setTableActionData(data: [])->assertTableActionDataSet(data: [])->assertHasNoTableActionErrors();
    $testable->callTableBulkAction('edit', [1])->assertHasTableBulkActionErrors();
    $testable->mountTableBulkAction('edit', [1])->setTableBulkActionData(data: [])->assertTableBulkActionDataSet(data: [])->assertHasNoTableBulkActionErrors();
    $testable->assertTableActionExists('replicate')->callTableAction('replicate', 1)->callMountedTableAction()->assertHasNoTableActionErrors();
    $testable->mountTableAction('edit', 1)->assertTableActionMounted('edit')->setTableActionData(data: []);
    $testable->selectTableRecords([1])->mountTableBulkAction('edit', [1])->assertHasNoTableBulkActionErrors();
    $testable->assertActionExists('edit')->assertHasNoFormComponentActionErrors();
    livewire('edit-post')->assertFormFieldIsVisible('title');
    Livewire::test('edit-post')->assertFormFieldIsDisabled('title');
    Livewire::test('edit-post')->callAction('save')->assertDispatched('saved')->assertActionHeld('save');
    $stored = livewire('edit-post');
    $stored->assertFormFieldIsHidden('secret');
    $testable->assertFormFieldDisabled('title');
    $testable->assertActionHalted('save');
    $testable->assertTableActionHalted('edit');
    $testable->assertTableBulkActionHalted('delete');
}

function factories(TestsForms $forms, TestsActions $actions, TestsTableActions $tableActions, TestsBulkActions $bulkActions): void
{
    $forms->assertFormFieldIsVisible();
    $actions->assertActionHeld();
    $tableActions->assertTableActionHeld();
    $tableActions->assertTableActionsExistInOrder();
    $callback = $bulkActions->assertTableBulkActionHeld(...);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedTestingAssertions;

use Closure;
use Filament\Actions\Testing\TestsActions;
use Filament\Forms\Testing\TestsForms;
use Filament\Tables\Testing\TestsActions as TestsTableActions;
use Filament\Tables\Testing\TestsBulkActions;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Filament\Tests\livewire;

function assertions(Testable $testable, ?Testable $nullable, array $arguments): void
{
    $testable->fillForm(state: ['title' => 'Changed'])->assertSchemaStateSet(state: ['title' => 'Changed'])->assertHasNoFormErrors();
    $testable->assertHasFormErrors(keys: ['title' => 'required']);
    $nullable?->fillForm([])->assertSchemaStateSet(state: []);
    $callback = Closure::bind(function (array|Closure $data): static {
        $this->assertSchemaStateSet($data);
        return $this;
    }, $testable, 'Livewire\Features\SupportTesting\Testable');
    $testable->assertFormFieldDisabled(key: 'title', form: 'editForm');
    $testable->assertFormFieldEnabled('title');
    $testable->assertFormFieldReadOnly('slug');
    $testable->assertFormFieldHidden('secret');
    $testable->assertFormFieldVisible('title');
    $testable->assertActionHalted(actions: 'save');
    $testable->assertActionHalted();
    $testable->assertTableActionHalted(actions: ['edit']);
    $testable->assertTableBulkActionHalted(actions: 'delete');
    $testable->assertTableRecordActionsExistInOrder(names: ['edit', 'delete']);
    $testable->assertTableRecordActionsExistInOrder(...$arguments);
    $nullable?->assertTableRecordActionsExistInOrder(['edit', 'delete']);
    $callback = $testable->assertTableRecordActionsExistInOrder(...);
    $testable->assertTableRecordActionsExistInOrder(['edit'])->assertTableRecordActionsExistInOrder(['delete']);
    $testable->assertTableRecordActionsExistInOrder(['edit'])->assertTableRecordActionsExistInOrder(['delete']);
    Livewire::test('edit-post')->assertTableRecordActionsExistInOrder(['edit', 'delete']);
    $nullable?->assertFormFieldVisible(key: 'title', form: null);
    $nullable?->assertFormFieldVisible('title')?->assertFormFieldEnabled('title');
    $testable->assertFormFieldDisabled(...$arguments);
    $callback = $testable->assertActionHalted(...);
    $callback = $testable->assertFormFieldVisible(...);
    $testable->assertFormFieldVisible('title')->assertFormFieldEnabled('title')->assertActionHalted('save');
    $testable->assertFormFieldVisible('title')->assertFormFieldEnabled('title');
    $testable->set('title', 'Hello')->assertFormFieldVisible('title');
    $testable->assertDispatched('saved')->assertActionHalted('save');
    $testable->fillForm(['title' => 'Hello'])->assertFormFieldEnabled('title');
    $testable->mountAction('save')->assertActionHalted('save');
    $testable->callAction('save')->assertActionHalted('save');
    $testable->mountTableAction('edit', 1)->assertTableActionHalted('edit');
    $testable->callTableAction('edit', 1)->assertTableActionHalted('edit');
    $testable->mountTableBulkAction('delete', [1])->assertTableBulkActionHalted('delete');
    $testable->callTableBulkAction('delete', [1])->assertTableBulkActionHalted('delete');
    $testable->callFormComponentAction('title', 'save')->assertHasFormErrors();
    $testable->mountFormComponentAction('title', 'save')->fillForm(state: [])->assertSchemaStateSet(state: [])->assertHasNoFormErrors();
    $testable->callInfolistAction('title', 'save')->assertHasFormErrors();
    $testable->mountInfolistAction('title', 'save')->fillForm(state: [])->assertSchemaStateSet(state: [])->assertHasNoFormErrors();
    $testable->callTableAction('edit', 1)->assertHasFormErrors();
    $testable->mountTableAction('edit', 1)->fillForm(state: [])->assertSchemaStateSet(state: [])->assertHasNoFormErrors();
    $testable->callTableBulkAction('edit', [1])->assertHasFormErrors();
    $testable->mountTableBulkAction('edit', [1])->fillForm(state: [])->assertSchemaStateSet(state: [])->assertHasNoFormErrors();
    $testable->assertTableActionExists('replicate')->callTableAction('replicate', 1)->callMountedTableAction()->assertHasNoFormErrors();
    $testable->mountTableAction('edit', 1)->assertTableActionMounted('edit')->fillForm(state: []);
    $testable->selectTableRecords([1])->mountTableBulkAction('edit', [1])->assertHasNoFormErrors();
    $testable->assertActionExists('edit')->assertHasNoFormErrors();
    livewire('edit-post')->assertFormFieldVisible('title');
    Livewire::test('edit-post')->assertFormFieldDisabled('title');
    Livewire::test('edit-post')->callAction('save')->assertDispatched('saved')->assertActionHalted('save');
    $stored = livewire('edit-post');
    $stored->assertFormFieldHidden('secret');
    $testable->assertFormFieldDisabled('title');
    $testable->assertActionHalted('save');
    $testable->assertTableActionHalted('edit');
    $testable->assertTableBulkActionHalted('delete');
}

function factories(TestsForms $forms, TestsActions $actions, TestsTableActions $tableActions, TestsBulkActions $bulkActions): void
{
    $forms->assertFormFieldVisible();
    $actions->assertActionHalted();
    $tableActions->assertTableActionHalted();
    $tableActions->assertTableRecordActionsExistInOrder();
    $callback = $bulkActions->assertTableBulkActionHalted(...);
}
