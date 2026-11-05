<?php

namespace Filament\Tables\Testing;

use Closure;
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
