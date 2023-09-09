<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

use function Livewire\store;

/**
 * @method HasTable instance()
 *
 * @mixin Testable
 * @mixin Testable
 */
class TestsBulkActions
{
    public function mountTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records): static {
            $name = $this->parseActionName($name);

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

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if ($this->instance()->mountedTableBulkAction === null) {
                $this->assertNotDispatched('open-modal');

                return $this;
            }

            $this->assertSet('mountedTableBulkAction', $name);

            $this->assertDispatched('open-modal', id: "{$this->instance()->getId()}-table-bulk-action");

            return $this;
        };
    }

    public function setTableBulkActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedTableBulkActionData.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertTableBulkActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedTableBulkActionData.') as $key => $value) {
                $this->assertSet($key, $value);
            }

            return $this;
        };
    }

    public function callTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionVisible($name);

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

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if ($this->get('mountedTableBulkAction') !== $action->getName()) {
                $this->assertDispatched('close-modal', id: "{$this->instance()->getId()}-table-bulk-action");
            }

            return $this;
        };
    }

    public function assertTableBulkActionExists(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table bulk action with name [{$name}] does not exist on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableBulkActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $livewire = $this->instance();
            $this->assertActionListInOrder(
                $names,
                $livewire->getTable()->getBulkActions(),
                'table bulk',
                BulkAction::class,
            );

            return $this;
        };
    }

    public function assertTableBulkActionVisible(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
            $name = $this->parseActionName($name);

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
        return function (string $name, string | array $color, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertTrue(
                $action->getColor() === $color,
                "Failed asserting that a table bulk action with name [{$name}] has [{$colorName}] color on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionDoesNotHaveColor(): Closure
    {
        return function (string $name, string | array $color, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $action = $this->instance()->getTable()->getBulkAction($name);

            $livewireClass = $this->instance()::class;

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertFalse(
                $action->getColor() === $color,
                "Failed asserting that a table bulk action with name [{$name}] does not have [{$colorName}] color on the [{$livewireClass}] component for record [{$record}]."
            );

            return $this;
        };
    }

    public function assertTableBulkActionMounted(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $this->assertSet('mountedTableBulkAction', $name);

            return $this;
        };
    }

    public function assertTableBulkActionNotMounted(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $this->assertNotSet('mountedTableBulkAction', $name);

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
