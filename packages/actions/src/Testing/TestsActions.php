<?php

namespace Filament\Actions\Testing;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

use function Livewire\store;

/**
 * @method HasActions instance()
 *
 * @mixin Testable
 */
class TestsActions
{
    public function mountAction(): Closure
    {
        return function (string | array $name, array $arguments = []): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            foreach ($name as $actionNestingIndex => $actionName) {
                $this->call(
                    'mountAction',
                    $actionName,
                    $arguments[$actionName] ?? ($actionNestingIndex ? [] : $arguments),
                );
            }

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedActions)) {
                $this->assertNotDispatched('open-modal');

                return $this;
            }

            $this->assertSet('mountedActions', $name);

            $this->assertDispatched('open-modal', id: "{$this->instance()->getId()}-action");

            return $this;
        };
    }

    public function unmountAction(): Closure
    {
        return function (): static {
            $this->call('unmountAction');

            return $this;
        };
    }

    public function setActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.') as $key => $value) {
                $this->assertSet($key, $value);
            }

            return $this;
        };
    }

    public function callAction(): Closure
    {
        return function (string | array $name, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionVisible($name, $arguments);

            /** @phpstan-ignore-next-line */
            $this->mountAction($name, $arguments);

            if (! $this->instance()->getMountedAction()) {
                return $this;
            }

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedAction($arguments);

            return $this;
        };
    }

    public function callMountedAction(): Closure
    {
        return function (array $arguments = []): static {
            $action = $this->instance()->getMountedAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedAction', $arguments);

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedActions)) {
                $this->assertDispatched('close-modal', id: "{$this->instance()->getId()}-action");
            }

            return $this;
        };
    }

    public function assertActionExists(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that an action with name [{$prettyName}] exists on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotExist(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            try {
                $action = $this->instance()->getAction($name);
            } catch (Exception $exception) {
                $action = null;
            }

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertNull(
                $action,
                message: "Failed asserting that an action with name [{$prettyName}] does not exist on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertActionVisible(): Closure
    {
        return function (string | array $name, array $arguments = []): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $action->arguments($arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$prettyName}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHidden(): Closure
    {
        return function (string | array $name, array $arguments = []): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $action->arguments($arguments);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$prettyName}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionEnabled(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->isEnabled(),
                message: "Failed asserting that an action with name [{$prettyName}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDisabled(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->isDisabled(),
                message: "Failed asserting that an action with name [{$prettyName}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasIcon(): Closure
    {
        return function (string | array $name, string $icon, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$prettyName}] has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveIcon(): Closure
    {
        return function (string | array $name, string $icon, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$prettyName}] does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasLabel(): Closure
    {
        return function (string | array $name, string $label, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$prettyName}] has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveLabel(): Closure
    {
        return function (string | array $name, string $label, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$prettyName}] does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasColor(): Closure
    {
        return function (string | array $name, string | array $color, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$prettyName}] has color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveColor(): Closure
    {
        return function (string | array $name, string | array $color, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$prettyName}] does not have color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasUrl(): Closure
    {
        return function (string | array $name, string $url, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$prettyName}] has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveUrl(): Closure
    {
        return function (string | array $name, string $url, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$prettyName}] does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string | array $name, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$prettyName}] should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string | array $name, $record = null): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', $name);

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$prettyName}] should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionMounted(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $this->assertSet('mountedActions', $name);

            return $this;
        };
    }

    public function assertActionNotMounted(): Closure
    {
        return function (string | array $name): static {
            /** @var array<string> $name */
            /** @phpstan-ignore-next-line */
            $name = $this->parseNestedActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $this->assertNotSet('mountedActions', $name);

            return $this;
        };
    }

    public function assertActionHalted(): Closure
    {
        return $this->assertActionMounted();
    }

    /**
     * @deprecated Use `assertActionHalted()` instead.
     */
    public function assertActionHeld(): Closure
    {
        return $this->assertActionHalted();
    }

    public function assertHasActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.' . $value];
                        }

                        return ['mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.' . $value];
                        }

                        return ['mountedActionsData.' . array_key_last($this->instance()->mountedActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertActionListInOrder(): Closure
    {
        return function (array $names, array $actions, string $actionType, string $actionClass): self {
            $livewireClass = $this->instance()::class;

            /** @var array<string> $names */
            $names = array_map(fn (string $name): string => $this->parseActionName($name), $names); // @phpstan-ignore-line
            $namesIndex = 0;

            $actions = array_reduce(
                $actions,
                function (array $carry, StaticAction | ActionGroup $action): array {
                    if ($action instanceof ActionGroup) {
                        return [
                            ...$carry,
                            ...$action->getFlatActions(),
                        ];
                    }

                    $carry[$action->getName()] = $action;

                    return $carry;
                },
                initial: [],
            );

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

    public function parseActionName(): Closure
    {
        return function (string $name): string {
            if (! class_exists($name)) {
                return $name;
            }

            if (! is_subclass_of($name, MountableAction::class)) {
                return $name;
            }

            return $name::getDefaultName();
        };
    }

    public function parseNestedActionName(): Closure
    {
        return function (string | array $name): array {
            if (is_string($name)) {
                $name = explode('.', $name);
            }

            foreach ($name as $actionNestingIndex => $actionName) {
                if (! class_exists($actionName)) {
                    continue;
                }

                if (! is_subclass_of($actionName, MountableAction::class)) {
                    continue;
                }

                $name[$actionNestingIndex] = $actionName::getDefaultName();
            }

            return $name;
        };
    }
}
