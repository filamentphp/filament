<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Testing\TestsActions as BaseTestsActions;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasTable instance()
 *
 * @mixin Testable
 * @mixin BaseTestsActions
 */
class TestsActions
{
    public function mountTableAction(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->mountAction($actions);

            return $this;
        };
    }

    public function unmountTableAction(): Closure
    {
        return function (): static {
            $this->unmountAction();

            return $this;
        };
    }

    public function callTableAction(): Closure
    {
        return function (string | array $actions, $record = null, array $data = [], array $arguments = []): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record, $arguments);

            $this->callAction($actions, $data);

            return $this;
        };
    }

    public function callMountedTableAction(): Closure
    {
        return function (array $arguments = []): static {
            $this->callMountedAction($arguments);

            return $this;
        };
    }

    public function assertTableRecordActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $this->assertActionListInOrder(
                $names,
                $this->instance()->getTable()->getRecordActions(),
                'table',
                Action::class,
            );

            return $this;
        };
    }

    public function assertTableHeaderActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $this->assertActionListInOrder(
                $names,
                $this->instance()->getTable()->getHeaderActions(),
                'table header',
                Action::class,
            );

            return $this;
        };
    }

    public function assertTableEmptyStateActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $this->assertActionListInOrder(
                $names,
                $this->instance()->getTable()->getEmptyStateActions(),
                'table empty state',
                Action::class,
            );

            return $this;
        };
    }

    public function parseNestedTableActions(): Closure
    {
        return function (string | array $actions, $record = null, array $arguments = []): array {
            /** @var array<array<string, mixed>> $actions */
            $actions = $this->parseNestedActions($actions, $arguments);

            $actions = array_map(
                function (array $action): array {
                    $action['context']['table'] = true;

                    return $action;
                },
                $actions,
            );

            if (blank($record)) {
                return $actions;
            }

            if (empty($actions)) {
                return $actions;
            }

            $firstAction = Arr::first($actions);
            $firstAction['context']['recordKey'] = $record;
            $actions[array_key_first($actions)] = $firstAction;

            return $actions;
        };
    }
}
