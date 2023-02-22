<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Actions\Testing\TestsActions as BaseTestsActions;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 * @mixin BaseTestsActions
 */
class TestsBulkActions
{
    public function mountTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionVisible($name);

            $records = array_map(
                function ($record) {
                    if ($record instanceof Model) {
                        return $this->instance()->getTableRecordKey($record);
                    }

                    return $record;
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            $this->call('mountTableBulkAction', $name, $records);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->instance()->mountedTableBulkAction === null) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                return $this;
            }

            $this->assertSet('mountedTableBulkAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$this->instance()->id}-table-bulk-action",
            ]);

            return $this;
        };
    }

    public function setTableBulkActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::prependKeysWith($data, 'mountedTableBulkActionData.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertTableBulkActionDataSet(): Closure
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

            if (! $this->instance()->getMountedTableBulkAction()) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setTableBulkActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedTableBulkAction($arguments);

            return $this;
        };
    }

    public function callMountedTableBulkAction(): Closure
    {
        return function (array $arguments = []): static {
            $action = $this->instance()->getMountedTableBulkAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedTableBulkAction', $arguments);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->get('mountedTableBulkAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$this->instance()->id}-table-bulk-action",
                ]);
            }

            return $this;
        };
    }

    public function assertTableBulkActionExists(): Closure
    {
        return function (string $name): static {
            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                BulkAction::class,
                $action,
                message: "Failed asserting that a table bulk action with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotExist(): Closure
    {
        return function (string $name): static {
            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table bulk action with name [{$name}] does not exist on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionVisible(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

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

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that a table bulk action with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionEnabled(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->isDisabled(),
                message: "Failed asserting that a table bulk action with name [{$name}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionDisabled(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->isDisabled(),
                message: "Failed asserting that a table bulk action with name [{$name}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionHasIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getIcon() === $icon,
                "Failed asserting that a table bulk action with name [{$name}] has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getIcon() === $icon,
                "Failed asserting that a table bulk action with name [{$name}] does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionHasLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getLabel() === $label,
                "Failed asserting that a table bulk action with name [{$name}] has label [{$label}] on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getLabel() === $label,
                "Failed asserting that a table bulk action with name [{$name}] does not have label [{$label}] on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionHasColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getColor() === $color,
                "Failed asserting that a table bulk action with name [{$name}] has color [{$color}] on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getColor() === $color,
                "Failed asserting that a table bulk action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionHalted(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $this->assertSet('mountedTableBulkAction', $name);

            return $this;
        };
    }

    /**
     * @deprecated Use `->assertTableBulkActionHalted()` instead.
     */
    public function assertTableBulkActionHeld(): Closure
    {
        return $this->assertTableBulkActionHalted();
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
