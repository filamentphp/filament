<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Support\Testing\TestsActions as BaseTestsActions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
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

            if ($record instanceof Model) {
                $record = $livewire->getTableRecordKey($record);
            }

            /** @phpstan-ignore-next-line */
            $this->assertTableActionVisible($name, $record);

            $this->call('mountTableAction', $name, $record);

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            if (! $action->shouldOpenModal()) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                $this->assertNotSet('mountedTableAction', $action->getName());

                return $this;
            }

            $this->assertSet('mountedTableAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$livewire->id}-table-action",
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

    public function assertTableActionDataSet(): Closure
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

            if (! $this->instance()->getMountedTableAction()) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setTableActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedTableAction($arguments);

            return $this;
        };
    }

    public function callMountedTableAction(): Closure
    {
        return function (array $arguments = []): static {
            $livewire = $this->instance();

            $action = $livewire->getMountedTableAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedTableAction', json_encode($arguments));

            if ($this->get('mountedTableAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$livewire->id}-table-action",
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

    public function assertTableActionDoesNotExist(): Closure
    {
        return function (string $name): static {
            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table action with name [{$name}] does not exist on the [{$livewireClass}] component.",
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

    public function assertTableActionEnabled(): Closure
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
                $action->isDisabled(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] is enabled on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionDisabled(): Closure
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
                $action->isEnabled(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] is disabled on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHasIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
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
                $action->getIcon() === $icon,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] has icon [{$icon}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
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
                $action->getIcon() === $icon,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] does not have icon [{$icon}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHasLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
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
                $action->getLabel() === $label,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] has label [{$label}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
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
                $action->getLabel() === $label,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] does not have label [{$label}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHasColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
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
                $action->getColor() === $color,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] has color [{$color}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] has color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
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
                $action->getColor() === $color,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component.",
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
}
