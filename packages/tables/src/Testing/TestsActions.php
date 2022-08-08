<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Support\Testing\TestsActions as BaseTestsActions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 * @mixin BaseTestsActions
 */
class TestsActions
{
    public function mountTableAction(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            if ($record instanceof Model) {
                $record = $livewire->getTableRecordKey($record);
            }

            /** @phpstan-ignore-next-line */
            $this->assertTableActionVisible($name, $record);

            $this->call('mountTableAction', $name, $record);

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            if (! $action->shouldOpenModal()) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                return $this;
            }

            $this->assertSet('mountedTableAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$livewireClass}-table-action",
            ]);

            return $this;
        };
    }

    public function setTableActionData(): Closure
    {
        return function (array $data): static {
            $this->set('mountedTableActionData', $data);

            return $this;
        };
    }

    public function assertTableActionData(): Closure
    {
        return function (array $data): static {
            foreach ($data as $key => $value) {
                $this->assertSet("mountedTableActionData.{$key}", $value);
            }

            return $this;
        };
    }

    public function callTableAction(): Closure
    {
        return function (string $name, $record = null, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->mountTableAction($name, $record);

            /** @phpstan-ignore-next-line */
            $this->callMountedTableAction($data, $arguments);

            return $this;
        };
    }

    public function callMountedTableAction(): Closure
    {
        return function (array $data = [], array $arguments = []): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getMountedTableAction();

            if (! $action->shouldOpenModal()) {
                $this->assertNotSet('mountedTableAction', $action->getName());

                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setTableActionData($data);

            $this->call('callMountedTableAction', json_encode($arguments));

            if ($this->get('mountedTableAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$livewireClass}-table-action",
                ]);
            }

            return $this;
        };
    }

    public function assertTableActionExists(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that a table action with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionVisible(): Closure
    {
        return function (string $name, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            if (! $record instanceof Model) {
                $record = $livewire->getTableRecord($record);
            }

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);
            $action->record($record);

            Assert::assertFalse(
                $action->isHidden(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] is visible on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHidden(): Closure
    {
        return function (string $name, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            if (! $record instanceof Model) {
                $record = $livewire->getTableRecord($record);
            }

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);
            $action->record($record);

            Assert::assertTrue(
                $action->isHidden(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] is hidden on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHeld(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            $this->assertSet('mountedTableAction', $name);

            return $this;
        };
    }

    public function assertHasTableActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "mountedTableActionData.{$value}"];
                        }

                        return ["mountedTableActionData.{$key}" => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoTableActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "mountedTableActionData.{$value}"];
                        }

                        return ["mountedTableActionData.{$key}" => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function mountTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionVisible($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $records = array_map(
                function ($record) use ($livewire) {
                    if ($record instanceof Model) {
                        return $livewire->getTableRecordKey($record);
                    }

                    return $record;
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            $this->call('mountTableBulkAction', $name, $records);

            $action = $livewire->getCachedTableBulkAction($name);

            if (! $action->shouldOpenModal()) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                return $this;
            }

            $this->assertSet('mountedTableBulkAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$livewireClass}-table-bulk-action",
            ]);

            return $this;
        };
    }

    public function setTableBulkActionData(): Closure
    {
        return function (array $data): static {
            $this->set('mountedTableBulkActionData', $data);

            return $this;
        };
    }

    public function assertTableBulkActionData(): Closure
    {
        return function (array $data): static {
            foreach ($data as $key => $value) {
                $this->assertSet("mountedTableBulkActionData.{$key}", $value);
            }

            return $this;
        };
    }

    public function callTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->mountTableBulkAction($name, $records);

            /** @phpstan-ignore-next-line */
            $this->callMountedTableBulkAction($data);

            return $this;
        };
    }

    public function callMountedTableBulkAction(): Closure
    {
        return function (array $data = [], array $arguments = []): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getMountedTableBulkAction();

            if (! $action->shouldOpenModal()) {
                $this->assertNotSet('mountedTableBulkAction', $action->getName());

                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setTableBulkActionData($data);

            $this->call('callMountedTableBulkAction', json_encode($arguments));

            if ($this->get('mountedTableBulkAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$livewireClass}-table-bulk-action",
                ]);
            }

            return $this;
        };
    }

    public function assertTableBulkActionExists(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

            Assert::assertInstanceOf(
                BulkAction::class,
                $action,
                message: "Failed asserting that a table bulk action with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionVisible(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that a table bulk action with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionHidden(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that a table bulk action with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionHeld(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $this->assertSet('mountedTableBulkAction', $name);

            return $this;
        };
    }

    public function assertHasTableBulkActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "mountedTableBulkActionData.{$value}"];
                        }

                        return ["mountedTableBulkActionData.{$key}" => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoTableBulkActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "mountedTableBulkActionData.{$value}"];
                        }

                        return ["mountedTableBulkActionData.{$key}" => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }
}
