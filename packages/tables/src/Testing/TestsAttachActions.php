<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasTable instance()
 *
 * @mixin Testable
 * @mixin Testable
 */
class TestsAttachActions
{
    public function assertTableAttachActionExists(): Closure
    {
        return function (string | array $name): static {
            $name = $this->parseNestedActionName($name);

            $action = $this->instance()->getTable()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertInstanceOf(
                AttachAction::class,
                $action,
                message: "Failed asserting that a table attach action with name [{$prettyName}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAttachActionDoesNotExist(): Closure
    {
        return function (string | array $name): static {
            $name = $this->parseNestedActionName($name);

            $action = $this->instance()->getTable()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table attach action with name [{$prettyName}] does not exist on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAttachActionIsMultipleSelect(): Closure
    {
        return function (string | array $name, bool $multiple, $record = null): static {
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableAttachActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name);
            $action->record($record);
            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->isMultiple(),
                message: filled($record) ?
                    "Failed asserting that a table attach action with name [{$prettyName}] is multiple [{$multiple}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table attach action with name [{$prettyName}] is not multiple [{$multiple}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAttachActionIsNotMultipleSelect(): Closure
    {
        return function (string | array $name, bool $multiple, $record = null): static {
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableAttachActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $action = $this->instance()->getTable()->getAction($name);
            $action->record($record);
            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->isMultiple(),
                message: filled($record) ?
                    "Failed asserting that a table action with name [{$prettyName}] is multiple [{$multiple}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table action with name [{$prettyName}] is multiple [{$multiple}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }
}
