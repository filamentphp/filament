<?php

namespace Filament\Tables\Testing;

use BackedEnum;
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

    public function setTableActionData(): Closure
    {
        return function (array $data): static {
            $this->fillForm($data);

            return $this;
        };
    }

    public function assertTableActionDataSet(): Closure
    {
        return function (array | Closure $data): static {
            $this->assertSchemaStateSet($data);

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

    public function assertTableActionExists(): Closure
    {
        return function (string | array $actions, ?Closure $checkActionUsing = null, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionExists($actions, $checkActionUsing);

            return $this;
        };
    }

    public function assertTableActionDoesNotExist(): Closure
    {
        return function (string | array $actions, ?Closure $checkActionUsing = null, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDoesNotExist($actions, $checkActionUsing);

            return $this;
        };
    }

    public function assertTableActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $this->assertActionListInOrder(
                $names,
                $this->instance()->getTable()->getActions(),
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

    public function assertTableActionVisible(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionVisible($actions);

            return $this;
        };
    }

    public function assertTableActionHidden(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionHidden($actions);

            return $this;
        };
    }

    public function assertTableActionEnabled(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionEnabled($actions);

            return $this;
        };
    }

    public function assertTableActionDisabled(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDisabled($actions);

            return $this;
        };
    }

    public function assertTableActionHasIcon(): Closure
    {
        return function (string | array $actions, string | BackedEnum $icon, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionHasIcon($actions, $icon);

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveIcon(): Closure
    {
        return function (string | array $actions, string | BackedEnum $icon, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDoesNotHaveIcon($actions, $icon);

            return $this;
        };
    }

    public function assertTableActionHasLabel(): Closure
    {
        return function (string | array $actions, string $label, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionHasLabel($actions, $label);

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveLabel(): Closure
    {
        return function (string | array $actions, string $label, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDoesNotHaveLabel($actions, $label);

            return $this;
        };
    }

    public function assertTableActionHasColor(): Closure
    {
        return function (string | array $actions, string | array $color, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionHasColor($actions, $color);

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveColor(): Closure
    {
        return function (string | array $actions, string | array $color, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDoesNotHaveColor($actions, $color);

            return $this;
        };
    }

    public function assertTableActionHasUrl(): Closure
    {
        return function (string | array $actions, string $url, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionHasUrl($actions, $url);

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveUrl(): Closure
    {
        return function (string | array $actions, string $url, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionDoesNotHaveUrl($actions, $url);

            return $this;
        };
    }

    public function assertTableActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionShouldOpenUrlInNewTab($actions);

            return $this;
        };
    }

    public function assertTableActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string | array $actions, $record = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions, $record);

            $this->assertActionShouldNotOpenUrlInNewTab($actions);

            return $this;
        };
    }

    public function assertTableActionMounted(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions);

            $this->assertActionMounted($actions);

            return $this;
        };
    }

    public function assertTableActionNotMounted(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableActions($actions);

            $this->assertActionNotMounted($actions);

            return $this;
        };
    }

    public function assertTableActionHalted(): Closure
    {
        return $this->assertTableActionMounted();
    }

    /**
     * @deprecated Use `assertTableActionHalted()` instead.
     */
    public function assertTableActionHeld(): Closure
    {
        return $this->assertTableActionHalted();
    }

    public function assertHasTableActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasFormErrors($keys);

            return $this;
        };
    }

    public function assertHasNoTableActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoFormErrors($keys);

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
