<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedTableMethods;

use Closure;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

function configure(Table $table, ?Table $nullableTable, array $arguments): void
{
    $table->actions(position: RecordActionsPosition::BeforeColumns, actions: [])
        ->pushActions(actions: [])
        ->actionsColumnLabel(label: 'Options')
        ->actionsAlignment(alignment: 'start')
        ->actionsPosition(position: null)
        ->bulkActions(actions: [])
        ->pushBulkActions(actions: [])
        ->toggleColumnsTriggerAction(callback: null)
        ->columnToggleFormColumns(columns: ['default' => 1, 'lg' => 3])
        ->columnToggleFormMaxHeight(height: '24rem')
        ->columnToggleFormWidth(width: Width::Large);
    $table->getActions();
    $table->getActionsPosition();
    $table->getActionsAlignment();
    $table->getActionsColumnLabel();
    $table->getBulkActions();
    $table->getColumnToggleFormColumns();
    $table->getColumnToggleFormMaxHeight();
    $table->getColumnToggleFormWidth();
    $table->getToggleColumnsTriggerAction();
    $nullableTable?->actions(actions: []);
    $nullableTable?->getColumnToggleFormWidth();
    $callback = $table->bulkActions(...);
    $table->ACTIONS(actions: []);
    $table->actions(...$arguments);
    $table->actionsAlignment();
    $table->actionsPosition();
    $table->groupedBulkActions(actions: []);
    $table->recordActions(actions: []);
    $method = 'actions';
    $table->{$method}([]);
    $table->{'actions'}([]);
    $callback = [$table, 'actions'];
}

class CustomTable extends Table
{
    public function configureActions(): static
    {
        $this->actions(actions: []);

        return parent::bulkActions(actions: []);
    }
}

class AliasOverridingTable extends Table
{
    public function actions(array | ActionGroup $items, RecordActionsPosition | string | Closure | null $placement = null): static
    {
        return $this;
    }
}

class InheritedAliasOverridingTable extends AliasOverridingTable {}

class ReplacementOverridingTable extends Table
{
    public function toolbarActions(array | ActionGroup $items = []): static
    {
        return $this;
    }

    public function configureActions(): static
    {
        return $this->bulkActions(actions: []);
    }
}

class InheritedReplacementOverridingTable extends ReplacementOverridingTable {}

function configureSubclasses(CustomTable $custom, ?InheritedAliasOverridingTable $alias, InheritedReplacementOverridingTable $replacement, CustomTable | AliasOverridingTable $union): void
{
    $custom->actions(actions: []);
    $alias?->actions(items: []);
    $replacement->bulkActions(actions: []);
    $union->actions([]);
}

class WidthOverridingTable extends Table
{
    public function getColumnToggleFormWidth(): ?string
    {
        return 'lg';
    }
}

function configureWidthOverride(WidthOverridingTable $table): void
{
    $table->getColumnToggleFormWidth();
}

function unrelated(Notification $notification, ActionGroup $group, UnrelatedComponent $component, Table | UnrelatedComponent $union, $unknown): void
{
    $notification->actions([]);
    $notification->getActions();
    $group->actions([]);
    $group->getActions();
    $component->actions(actions: []);
    $union->actions([]);
    $unknown->bulkActions([]);
    Table::actions([]);
}

class UnrelatedComponent
{
    public function actions(array $actions): void {}
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedTableMethods;

use Closure;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

function configure(Table $table, ?Table $nullableTable, array $arguments): void
{
    $table->recordActions(position: RecordActionsPosition::BeforeColumns, actions: [])
        ->pushRecordActions(actions: [])
        ->recordActionsColumnLabel(label: 'Options')
        ->recordActionsAlignment(alignment: 'start')
        ->recordActionsPosition(position: null)
        ->toolbarActions(actions: [])
        ->pushToolbarActions(actions: [])
        ->columnManagerTriggerAction(callback: null)
        ->columnManagerColumns(columns: ['default' => 1, 'lg' => 3])
        ->columnManagerMaxHeight(height: '24rem')
        ->columnManagerWidth(width: Width::Large);
    $table->getRecordActions();
    $table->getRecordActionsPosition();
    $table->getRecordActionsAlignment();
    $table->getRecordActionsColumnLabel();
    $table->getToolbarActions();
    $table->getColumnManagerColumns();
    $table->getColumnManagerMaxHeight();
    $table->getColumnManagerWidth();
    $table->getColumnManagerTriggerAction();
    $nullableTable?->recordActions(actions: []);
    $nullableTable?->getColumnManagerWidth();
    $callback = $table->toolbarActions(...);
    $table->recordActions(actions: []);
    $table->recordActions(...$arguments);
    $table->recordActionsAlignment();
    $table->recordActionsPosition();
    $table->groupedBulkActions(actions: []);
    $table->recordActions(actions: []);
    $method = 'actions';
    $table->{$method}([]);
    $table->{'actions'}([]);
    $callback = [$table, 'actions'];
}

class CustomTable extends Table
{
    public function configureActions(): static
    {
        $this->recordActions(actions: []);

        return parent::toolbarActions(actions: []);
    }
}

class AliasOverridingTable extends Table
{
    public function actions(array | ActionGroup $items, RecordActionsPosition | string | Closure | null $placement = null): static
    {
        return $this;
    }
}

class InheritedAliasOverridingTable extends AliasOverridingTable {}

class ReplacementOverridingTable extends Table
{
    public function toolbarActions(array | ActionGroup $items = []): static
    {
        return $this;
    }

    public function configureActions(): static
    {
        return $this->bulkActions(actions: []);
    }
}

class InheritedReplacementOverridingTable extends ReplacementOverridingTable {}

function configureSubclasses(CustomTable $custom, ?InheritedAliasOverridingTable $alias, InheritedReplacementOverridingTable $replacement, CustomTable | AliasOverridingTable $union): void
{
    $custom->recordActions(actions: []);
    $alias?->actions(items: []);
    $replacement->bulkActions(actions: []);
    $union->actions([]);
}

class WidthOverridingTable extends Table
{
    public function getColumnToggleFormWidth(): ?string
    {
        return 'lg';
    }
}

function configureWidthOverride(WidthOverridingTable $table): void
{
    $table->getColumnToggleFormWidth();
}

function unrelated(Notification $notification, ActionGroup $group, UnrelatedComponent $component, Table | UnrelatedComponent $union, $unknown): void
{
    $notification->actions([]);
    $notification->getActions();
    $group->actions([]);
    $group->getActions();
    $component->actions(actions: []);
    $union->actions([]);
    $unknown->bulkActions([]);
    Table::actions([]);
}

class UnrelatedComponent
{
    public function actions(array $actions): void {}
}
