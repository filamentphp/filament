<?php

namespace Filament\Support\Testing;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
use Illuminate\Testing\Assert;
use Livewire\Component;
use Livewire\Testing\TestableLivewire;

/**
 * @method Component instance()
 *
 * @mixin TestableLivewire
 */
class TestsActions
{
    public function parseActionName(): Closure
    {
        return function (string $name): string {
            if (! class_exists($name)) {
                return $name;
            }

            if (! is_subclass_of($name, BaseAction::class)) {
                return $name;
            }

            return $name::getDefaultName();
        };
    }

    public function assertActionListInOrder(): Closure
    {
        return function (array $names, array $actions, string $actionType, string $actionClass): self {
            $livewireClass = $this->instance()::class;

            /** @var array<string> $names */
            $names = array_map(fn ($name) => $this->parseActionName($name), $names); // @phpstan-ignore-line
            $namesIndex = 0;

            foreach ($actions as $actionName => $action) {
                if ($namesIndex === count($names)) {
                    break;
                }

                if ($names[$namesIndex] !== $actionName) {
                    continue;
                }

                Assert::assertInstanceOf(
                    $actionClass,
                    $action,
                    message: "Failed asserting that a {$actionType} action with name [{$actionName}] exists on the [{$livewireClass}] component.",
                );

                $namesIndex++;
            }

            Assert::assertEquals(
                count($names),
                $namesIndex,
                message: "Failed asserting that a {$actionType} actions with names [" . implode(', ', $names) . "] exist in order on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }
}
