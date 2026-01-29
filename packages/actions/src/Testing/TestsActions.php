<?php

namespace Filament\Actions\Testing;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ActionName;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use LogicException;
use ReflectionClass;

use function Livewire\store;

/**
 * @method Component&HasActions instance()
 *
 * @mixin Testable
 */
class TestsActions
{
    public function mountAction(): Closure
    {
        return function (string | TestAction | array $actions, array $arguments = []): static {
            $initialMountedActionsCount = count($this->instance()->mountedActions);

            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedActions($actions, $arguments);

            foreach ($actions as $action) {
                $this->call(
                    'mountAction',
                    $action['name'],
                    $action['arguments'] ?? [],
                    $action['context'] ?? [],
                );
            }

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
            $this->fillForm($data);

            return $this;
        };
    }

    public function assertActionDataSet(): Closure
    {
        return function (array | Closure $data): static {
            $this->assertSchemaStateSet($data);

            return $this;
        };
    }

    public function callAction(): Closure
    {
        return function (string | TestAction | array $actions, array $data = [], array $arguments = []): static {
            $initialMountedActionsCount = count($this->instance()->mountedActions);

            /** @phpstan-ignore-next-line */
            $this->assertActionVisible($actions, $arguments);

            /** @var array<array<string, mixed>> $parsedActions */
            /** @phpstan-ignore-next-line */
            $parsedActions = $this->parseNestedActions($actions, $arguments);

            /** @phpstan-ignore-next-line */
            $this->mountAction($actions, $arguments);

            if (count($this->instance()->mountedActions) !== ($initialMountedActionsCount + count(Arr::wrap($actions)))) {
                return $this;
            }

            $lastParsedAction = Arr::last($parsedActions);
            $lastMountedActionIndex = count($this->instance()->mountedActions) - 1;

            if (
                $lastMountedActionIndex >= 0 &&
                ($this->instance()->mountedActions[$lastMountedActionIndex]['name'] ?? null) !== ($lastParsedAction['name'] ?? null)
            ) {
                return $this;
            }

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (filled($data)) {
                /** @phpstan-ignore-next-line */
                $this->fillForm($data);
            }

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

            return $this;
        };
    }

    public function assertActionExists(): Closure
    {
        return function (string | TestAction | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null, array $arguments = []): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedActions($actions, $arguments);

            $action = $this->instance()->getAction([
                ...$this->instance()->mountedActions,
                ...$actions,
            ]);

            if ($action && filled($arguments = Arr::last($actions)['arguments'] ?? [])) {
                $action->mergeArguments($arguments);
            }

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::pluck($actions, 'name'));

            Assert::assertInstanceOf(
                Action::class,
                $action,
                $generateMessageUsing ?
                    $generateMessageUsing($prettyName, $livewireClass) :
                    "Failed asserting that an action with name [{$prettyName}] exists on the [{$livewireClass}] component.",
            );

            if ($checkActionUsing) {
                Assert::assertTrue(
                    $checkActionUsing($action),
                    $generateMessageUsing ?
                        $generateMessageUsing($prettyName, $livewireClass) :
                        "Failed asserting that an action with the name [{$prettyName}] and provided configuration exists on the [{$livewireClass}] component.",
                );
            }

            return $this;
        };
    }

    public function assertActionDoesNotExist(): Closure
    {
        return function (string | TestAction | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null): static {
            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedActions($actions);

            try {
                $action = $this->instance()->getAction($actions); /** @phpstan-ignore argument.type */
            } catch (ActionNotResolvableException $exception) {
                Assert::assertNull(null);

                return $this;
            }

            if ($action && filled($arguments = Arr::last($actions)['arguments'] ?? [])) {
                $action->mergeArguments($arguments);
            }

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::pluck($actions, 'name'));

            if (! $action) {
                Assert::assertNull($action);
            }

            if ($checkActionUsing) {
                Assert::assertFalse(
                    $checkActionUsing($action),
                    $generateMessageUsing ?
                        $generateMessageUsing($prettyName, $livewireClass) :
                        "Failed asserting that an action with the name [{$prettyName}] and provided configuration does not exist on the [{$livewireClass}] component.",
                );
            } else {
                Assert::assertNotInstanceOf(
                    Action::class,
                    $action,
                    $generateMessageUsing ?
                        $generateMessageUsing($prettyName, $livewireClass) :
                        "Failed asserting that an action with the name [{$prettyName}] does not exist on the [{$livewireClass}] component.",
                );
            }

            return $this;
        };
    }

    public function assertActionVisible(): Closure
    {
        return function (string | TestAction | array $actions, array $arguments = []): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->isVisible(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] is visible on the [{$livewireClass}] component.",
                arguments: $arguments,
            );

            return $this;
        };
    }

    public function assertActionHidden(): Closure
    {
        return function (string | TestAction | array $actions, array $arguments = []): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->isHidden(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] is hidden on the [{$livewireClass}] component.",
                arguments: $arguments,
            );

            return $this;
        };
    }

    public function assertActionEnabled(): Closure
    {
        return function (string | TestAction | array $actions): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->isEnabled(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDisabled(): Closure
    {
        return function (string | TestAction | array $actions): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->isDisabled(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasIcon(): Closure
    {
        return function (string | TestAction | array $actions, string | BackedEnum $icon): static {

            $iconValue = $icon instanceof BackedEnum ? $icon->value : $icon;

            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getIcon() === $icon,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] has icon [{$iconValue}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveIcon(): Closure
    {
        return function (string | TestAction | array $actions, string | BackedEnum $icon): static {

            $iconValue = $icon instanceof BackedEnum ? $icon->value : $icon;

            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getIcon() !== $icon,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] does not have icon [{$iconValue}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasLabel(): Closure
    {
        return function (string | TestAction | array $actions, string $label): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getLabel() === $label,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveLabel(): Closure
    {
        return function (string | TestAction | array $actions, string $label): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getLabel() !== $label,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasColor(): Closure
    {
        return function (string | TestAction | array $actions, string | array $color): static {
            $colorName = is_string($color) ? $color : 'custom';

            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getColor() === $colorName,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] has color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveColor(): Closure
    {
        return function (string | TestAction | array $actions, string | array $color): static {
            $colorName = is_string($color) ? $color : 'custom';

            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getColor() !== $colorName,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] does not have color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasUrl(): Closure
    {
        return function (string | TestAction | array $actions, string $url): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getUrl() === $url,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveUrl(): Closure
    {
        return function (string | TestAction | array $actions, string $url): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->getUrl() !== $url,
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string | TestAction | array $actions): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => $action->shouldOpenUrlInNewTab(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string | TestAction | array $actions): static {
            $this->assertActionExists(
                $actions,
                checkActionUsing: fn (Action $action): bool => ! $action->shouldOpenUrlInNewTab(),
                generateMessageUsing: fn (string $prettyName, string $livewireClass): string => "Failed asserting that an action with name [{$prettyName}] should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionMounted(): Closure
    {
        return function (string | TestAction | array $actions = []): static {
            if (empty($actions)) {
                $this->assertNotSet('mountedActions', []);

                return $this;
            }

            $originalActions = Arr::wrap($actions);

            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedActions($actions, areRelativeToMountedActions: false);

            $actionNestingIndexOffset = count($this->instance()->mountedActions) - count($actions);

            foreach ($actions as $actionNestingIndex => $action) {
                $actionNestingIndex += $actionNestingIndexOffset;

                $this->assertSet(
                    "mountedActions.{$actionNestingIndex}.name",
                    $action['name'],
                );

                if (array_key_exists('arguments', $action)) {
                    $this->assertSet(
                        "mountedActions.{$actionNestingIndex}.arguments",
                        $action['arguments'],
                    );
                }

                if (($originalAction = array_shift($originalActions)) instanceof TestAction) {
                    Assert::assertTrue(
                        $originalAction->checkArguments($this->instance()->mountedActions[$actionNestingIndex]['arguments'] ?? []),
                        "Failed asserting that the mounted arguments for the action [{$action['name']}] match the expected arguments.",
                    );
                }

                $this->assertSet(
                    "mountedActions.{$actionNestingIndex}.context",
                    $action['context'] ?? [],
                );
            }

            return $this;
        };
    }

    public function assertActionNotMounted(): Closure
    {
        return function (string | TestAction | array $actions = []): static {
            if (empty($actions)) {
                $this->assertSet('mountedActions', []);

                return $this;
            }

            $originalActions = Arr::wrap($actions);

            /** @var array<array<string, mixed>> $actions */
            /** @phpstan-ignore-next-line */
            $actions = $this->parseNestedActions($actions, areRelativeToMountedActions: false);

            $actionNestingIndexOffset = count($this->instance()->mountedActions) - count($actions);

            foreach ($actions as $actionNestingIndex => $action) {
                $actionNestingIndex += $actionNestingIndexOffset;

                if (($this->instance()->mountedActions[$actionNestingIndex]['name'] ?? null) !== $action['name']) {
                    return $this;
                }

                if (
                    array_key_exists('arguments', $action) &&
                    (($this->instance()->mountedActions[$actionNestingIndex]['arguments'] ?? null) !== $action['arguments'])
                ) {
                    return $this;
                }

                if (
                    (($originalAction = array_shift($originalActions)) instanceof TestAction) &&
                    (! $originalAction->checkArguments($this->instance()->mountedActions[$actionNestingIndex]['arguments'] ?? []))
                ) {
                    return $this;
                }

                if (($this->instance()->mountedActions[$actionNestingIndex]['context'] ?? null) !== $action['context']) {
                    return $this;
                }
            }

            Assert::assertFalse(
                true,
                'Failed asserting that the action is not mounted.',
            );

            return $this;
        };
    }

    public function assertMountedActionModalSee(): Closure
    {
        return function (string | array $values, $escape = true) {
            /**
             * @var string $html
             *
             * @phpstan-ignore-next-line
             */
            $html = $this->getMountedActionModalHtml();

            foreach (Arr::wrap($values) as $value) {
                Assert::assertStringContainsString(
                    $escape ? e($value) : $value,
                    $html
                );
            }

            return $this;
        };
    }

    public function assertMountedActionModalDontSee(): Closure
    {
        return function (string | array $values, bool $escape = true) {
            /**
             * @var string $html
             *
             * @phpstan-ignore-next-line
             */
            $html = $this->getMountedActionModalHtml();

            foreach (Arr::wrap($values) as $value) {
                Assert::assertStringNotContainsString(
                    $escape ? e($value) : $value,
                    $html
                );
            }

            return $this;
        };
    }

    public function assertMountedActionModalSeeHtml(): Closure
    {
        return function (string | array $values) {
            /**
             * @var string $html
             *
             * @phpstan-ignore-next-line
             */
            $html = $this->getMountedActionModalHtml();

            foreach (Arr::wrap($values) as $value) {
                Assert::assertStringContainsString(
                    $value,
                    $html
                );
            }

            return $this;
        };
    }

    public function assertMountedActionModalDontSeeHtml(): Closure
    {
        return function (string | array $values) {
            /**
             * @var string $html
             *
             * @phpstan-ignore-next-line
             */
            $html = $this->getMountedActionModalHtml();

            foreach (Arr::wrap($values) as $value) {
                Assert::assertStringNotContainsString(
                    $value,
                    $html
                );
            }

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
            $this->assertHasFormErrors($keys);

            return $this;
        };
    }

    public function assertHasNoActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoFormErrors($keys);

            return $this;
        };
    }

    public function assertActionListInOrder(): Closure
    {
        return function (array $names, array $actions, string $actionType, string $actionClass): self {
            $livewireClass = $this->instance()::class;

            /** @var array<string> $names */
            $names = array_map(function (string $name): string {
                if (! class_exists($name)) {
                    return $name;
                }

                if ($actionClassNameAttributes = (new ReflectionClass($name))->getAttributes(ActionName::class)) {
                    $name = (string) Arr::first($actionClassNameAttributes)->newInstance();
                }

                if (! is_subclass_of($name, Action::class)) {
                    return $name;
                }

                return $name::getDefaultName();
            }, $names);
            $namesIndex = 0;

            $actions = array_reduce(
                $actions,
                function (array $carry, Action | ActionGroup $action): array {
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
                    "Failed asserting that a {$actionType} action with name [{$actionName}] exists on the [{$livewireClass}] component.",
                );

                $namesIndex++;
            }

            Assert::assertEquals(
                count($names),
                $namesIndex,
                "Failed asserting that a {$actionType} actions with names [" . implode(', ', $names) . "] exist in order on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function parseNestedActions(): Closure
    {
        return function (string | TestAction | array $actions, array $arguments = [], bool $areRelativeToMountedActions = true): array {
            $initialMountedActionsCount = $areRelativeToMountedActions ? count($this->instance()->mountedActions) : 0;

            if (is_string($actions)) {
                $actions = explode('.', $actions);
            } elseif (
                ($actions instanceof TestAction) ||
                array_key_exists('name', $actions)
            ) {
                $actions = [$actions];
            }

            $areArgumentsKeyedByActionName = false;

            foreach ($actions as $actionNestingIndex => $action) {
                if (is_string($action)) {
                    $action = [
                        'name' => $action,
                    ];
                } elseif ($action instanceof TestAction) {
                    $action = $action->toArray(defaultSchema: ($initialMountedActionsCount + $actionNestingIndex) ? ('mountedActionSchema' . ($initialMountedActionsCount + $actionNestingIndex - 1)) : $this->instance()->getDefaultTestingSchemaName());
                }

                $actionName = $action['name'] ?? throw new LogicException("Action name at index [{$actionNestingIndex}] is not specified.");

                if (
                    class_exists($actionName) &&
                    ($actionClassNameAttributes = (new ReflectionClass($actionName))->getAttributes(ActionName::class))
                ) {
                    $action['name'] = $actionName = (string) Arr::first($actionClassNameAttributes)->newInstance();
                }

                if (
                    class_exists($actionName) &&
                    is_subclass_of($actionName, Action::class)
                ) {
                    $action['name'] = $actionName = $actionName::getDefaultName();
                }

                if (filled($arguments) && (! array_key_exists('arguments', $action))) {
                    if (array_key_exists($actionName, $arguments)) {
                        $action['arguments'] = $arguments[$actionName];

                        $areArgumentsKeyedByActionName = true;
                    } elseif (! $areArgumentsKeyedByActionName) {
                        $action['arguments'] = $arguments;
                    }
                }

                if (
                    ($action['context']['table'] ?? false) &&
                    filled($tableRecordKey = $action['context']['recordKey'] ?? null) &&
                    ($tableRecordKey instanceof Model)
                ) {
                    $action['context']['recordKey'] = $this->instance()->getTableRecordKey($tableRecordKey);
                }

                $actions[$actionNestingIndex] = $action;
            }

            return $actions;
        };
    }

    /**
     * @internal
     */
    public function getMountedActionModalHtml(): Closure
    {
        return function (): string {
            $partials = data_get($this->lastState->getEffects(), 'partials', []);

            $partialName = 'action-modals';

            if (array_key_exists($partialName, $partials)) {
                return $partials[$partialName];
            }

            $nestingIndex = count($this->instance()->mountedActions) - 1;
            $partialName = "{$partialName}.{$nestingIndex}";

            if (array_key_exists($partialName, $partials)) {
                return $partials[$partialName];
            }

            Assert::fail('No mounted action modal content was found.');
        };
    }
}
