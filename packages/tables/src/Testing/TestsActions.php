<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Actions\Testing\TestsActions as BaseTestsActions;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

            if ($record instanceof Model) {
                $record = $this->instance()->getTableRecordKey($record);
            }

            /** @phpstan-ignore-next-line */
            $this->assertTableActionVisible($name, $record);

            $this->call('mountTableAction', $name, $record);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->instance()->mountedTableAction === null) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                return $this;
            }

            $this->assertSet('mountedTableAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$this->instance()->id}-table-action",
            ]);

            return $this;
        };
    }

    public function setTableActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::prependKeysWith($data, 'mountedTableActionsData.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertTableActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach ($data as $key => $value) {
                $this->assertSet("mountedTableActionsData.{$key}", $value);
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
            $action = $this->instance()->getMountedTableAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedTableAction', $arguments);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->get('mountedTableAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$this->instance()->id}-table-action",
                ]);
            }

            return $this;
        };
    }

    public function assertTableActionExists(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table action with name [{$name}] does not exist on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $livewire = $this->instance();
            $this->assertActionListInOrder(
                $names,
                $livewire->getTable()->getActions(),
                'table',
                Action::class,
            );

            return $this;
        };
    }

    public function assertTableHeaderActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $livewire = $this->instance();
            $this->assertActionListInOrder(
                $names,
                $livewire->getTable()->getHeaderActions(),
                'table header',
                Action::class,
            );

            return $this;
        };
    }

    public function assertTableEmptyStateActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $livewire = $this->instance();
            $this->assertActionListInOrder(
                $names,
                $livewire->getTable()->getEmptyStateActions(),
                'table empty state',
                Action::class,
            );

            return $this;
        };
    }

    public function assertTableActionVisible(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

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
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getColor() === $color,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHasUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] has URL [{$url}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionDoesNotHaveUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] does not have URL [{$url}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] should open url in new tab on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name) ?? $this->instance()->getTable()->getEmptyStateAction($name) ?? $this->instance()->getTable()->getHeaderAction($name);
            $action->record($record);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$name}] should not open url in new tab on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$name}] should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableActionHalted(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableActionExists($name);

            $this->assertSet('mountedTableAction', $name);

            return $this;
        };
    }

    /**
     * @deprecated Use `->assertTableActionHalted()` instead.
     */
    public function assertTableActionHeld(): Closure
    {
        return $this->assertTableActionHalted();
    }

    public function assertHasTableActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "mountedTableActionsData.{$value}"];
                        }

                        return ["mountedTableActionsData.{$key}" => $value];
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
                            return [$key => "mountedTableActionsData.{$value}"];
                        }

                        return ["mountedTableActionsData.{$key}" => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }
}
