<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\AssociateAction;
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
class TestsAssociateActions
{
    public function assertTableAssociateActionExists(): Closure
    {
        return function (string | array $name): static {
            $name = $this->parseNestedActionName($name);

            $action = $this->instance()->getTable()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertInstanceOf(
                AssociateAction::class,
                $action,
                message: "Failed asserting that a table associate action with name [{$prettyName}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAssociateActionDoesNotExist(): Closure
    {
        return function (string | array $name): static {
            $name = $this->parseNestedActionName($name);

            $action = $this->instance()->getTable()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertNull(
                $action,
                message: "Failed asserting that a table associate action with name [{$prettyName}] does not exist on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAssociateActionIsMultipleSelect(): Closure
    {
        return function (string | array $name, bool $multiple, $record = null): static {
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableAssociateActionExists($name);

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
                    "Failed asserting that a table associate action with name [{$prettyName}] is multiple [{$multiple}] on the [{$livewireClass}] component for record [{$record}]." :
                    "Failed asserting that a table associate action with name [{$prettyName}] is multiple [{$multiple}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableAssociateActionIsNotMultipleSelect(): Closure
    {
        return function (string | array $name, bool $multiple, $record = null): static {
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertTableAssociateActionExists($name);

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
