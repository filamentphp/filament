<?php

namespace Filament\Tables\Testing;

use BackedEnum;
use Closure;
use Filament\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasTable instance()
 *
 * @mixin Testable
 */
class TestsBulkActions
{
    public function selectTableRecords(): Closure
    {
        return function (array | Collection $records): static {
            $this->set('selectedTableRecords', array_map(
                function ($record) {
                    if ($record instanceof Model) {
                        return $this->instance()->getTableRecordKey($record);
                    }

                    return $record;
                },
                $records instanceof Collection ? $records->all() : $records,
            ));

            return $this;
        };
    }

    public function mountTableBulkAction(): Closure
    {
        return function (string | array $actions, array | Collection $records): static {
            /** @phpstan-ignore-next-line */
            $this->selectTableRecords($records);

            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->mountAction($actions);

            return $this;
        };
    }

    public function setTableBulkActionData(): Closure
    {
        return function (array $data): static {
            $this->fillForm($data);

            return $this;
        };
    }

    public function assertTableBulkActionDataSet(): Closure
    {
        return function (array | Closure $data): static {
            $this->assertSchemaStateSet($data);

            return $this;
        };
    }

    public function callTableBulkAction(): Closure
    {
        return function (string | array $actions, array | Collection $records, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->selectTableRecords($records);

            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->callAction($actions, $data, $arguments);

            return $this;
        };
    }

    public function callMountedTableBulkAction(): Closure
    {
        return function (array $arguments = []): static {
            $this->callMountedAction($arguments);

            return $this;
        };
    }

    public function assertTableBulkActionExists(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionExists($actions);

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotExist(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionDoesNotExist($actions);

            return $this;
        };
    }

    public function assertTableBulkActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $this->assertActionListInOrder(
                $names,
                $this->instance()->getTable()->getBulkActions(),
                'table bulk',
                BulkAction::class,
            );

            return $this;
        };
    }

    public function assertTableBulkActionVisible(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionVisible($actions);

            return $this;
        };
    }

    public function assertTableBulkActionHidden(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionHidden($actions);

            return $this;
        };
    }

    public function assertTableBulkActionEnabled(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionEnabled($actions);

            return $this;
        };
    }

    public function assertTableBulkActionDisabled(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionDisabled($actions);

            return $this;
        };
    }

    public function assertTableBulkActionHasIcon(): Closure
    {
        return function (string | array $actions, string | BackedEnum $icon): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionHasIcon($actions, $icon);

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveIcon(): Closure
    {
        return function (string | array $actions, string | BackedEnum $icon): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionDoesNotHaveIcon($actions, $icon);

            return $this;
        };
    }

    public function assertTableBulkActionHasLabel(): Closure
    {
        return function (string | array $actions, string $label): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionHasLabel($actions, $label);

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveLabel(): Closure
    {
        return function (string | array $actions, string $label): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionDoesNotHaveLabel($actions, $label);

            return $this;
        };
    }

    public function assertTableBulkActionHasColor(): Closure
    {
        return function (string | array $actions, string | array $color): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionHasColor($actions, $color);

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveColor(): Closure
    {
        return function (string | array $actions, string | array $color): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionDoesNotHaveColor($actions, $color);

            return $this;
        };
    }

    public function assertTableBulkActionMounted(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionMounted($actions);

            return $this;
        };
    }

    public function assertTableBulkActionNotMounted(): Closure
    {
        return function (string | array $actions): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedTableBulkActions($actions);

            $this->assertActionNotMounted($actions);

            return $this;
        };
    }

    public function assertTableBulkActionHalted(): Closure
    {
        return $this->assertTableBulkActionMounted();
    }

    /**
     * @deprecated Use `assertTableBulkActionHalted()` instead.
     */
    public function assertTableBulkActionHeld(): Closure
    {
        return $this->assertTableBulkActionHalted();
    }

    public function assertHasTableBulkActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasFormErrors($keys);

            return $this;
        };
    }

    public function assertHasNoTableBulkActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoFormErrors($keys);

            return $this;
        };
    }

    public function parseNestedTableBulkActions(): Closure
    {
        return function (string | array $actions): array {
            /** @var array<array<string, mixed>> $actions */
            $actions = $this->parseNestedTableActions($actions);

            return array_map(
                function (array $action): array {
                    $action['context']['bulk'] = true;

                    return $action;
                },
                $actions,
            );
        };
    }
}
