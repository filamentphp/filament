<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Support\Testing\TestsActions as BaseTestsActions;
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
class TestsBulkActions
{
    public function mountTableBulkAction(): Closure
    {
        return function (string $name, array | Collection $records): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionVisible($name);

            $livewire = $this->instance();

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

                $this->assertNotSet('mountedTableBulkAction', $action->getName());

                return $this;
            }

            $this->assertSet('mountedTableBulkAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$livewire->id}-table-bulk-action",
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
            $livewire = $this->instance();

            $action = $livewire->getMountedTableBulkAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedTableBulkAction', json_encode($arguments));

            if ($this->get('mountedTableBulkAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$livewire->id}-table-bulk-action",
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

    public function assertTableBulkActionDoesNotExist(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

    public function assertTableBulkActionEnabled(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableBulkActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

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

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableBulkAction($name);

            Assert::assertFalse(
                $action->getColor() === $color,
                "Failed asserting that a table bulk action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component for record [{$record}]."
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
