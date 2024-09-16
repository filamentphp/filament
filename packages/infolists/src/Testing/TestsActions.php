<?php

namespace Filament\Infolists\Testing;

use Closure;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Contracts\HasInfolists;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

use function Livewire\store;

/**
 * @method HasInfolists instance()
 *
 * @mixin Testable
 */
class TestsActions
{
    public function mountInfolistAction(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            $name = $this->parseNestedActionName($name);

            foreach ($name as $actionNestingIndex => $actionName) {
                $this->call(
                    'mountInfolistAction',
                    $actionName,
                    $actionNestingIndex ? null : $component,
                    $actionNestingIndex ? null : $infolistName,
                );
            }

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedInfolistActions)) {
                $this->assertNotDispatched('open-modal');

                return $this;
            }

            $this->assertSet('mountedInfolistActionsComponent', $component);
            $this->assertSet('mountedInfolistActions', $name);

            $this->assertDispatched('open-modal', id: "{$this->instance()->getId()}-infolist-action");

            return $this;
        };
    }

    public function unmountInfolistAction(): Closure
    {
        return function (): static {
            $this->call('unmountInfolistAction');

            return $this;
        };
    }

    public function setInfolistActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertInfolistActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach (Arr::dot($data, prepend: 'mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.') as $key => $value) {
                $this->assertSet($key, $value);
            }

            return $this;
        };
    }

    public function callInfolistAction(): Closure
    {
        return function (string $component, string | array $name, array $data = [], array $arguments = [], string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionVisible($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            $this->mountInfolistAction($component, $name, $infolistName);

            if (! $this->instance()->getMountedInfolistAction()) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setInfolistActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedInfolistAction($arguments);

            return $this;
        };
    }

    public function callMountedInfolistAction(): Closure
    {
        return function (array $arguments = []): static {
            $action = $this->instance()->getMountedInfolistAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedInfolistAction', $arguments);

            if (store($this->instance())->has('redirect')) {
                return $this;
            }

            if (! count($this->instance()->mountedInfolistActions)) {
                $this->assertDispatched('close-modal', id: "{$this->instance()->getId()}-infolist-action");
            }

            return $this;
        };
    }

    public function assertInfolistActionExists(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name, $infolistName);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that a infolist action with name [{$prettyName}] is registered to [{$component->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDoesNotExist(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name, $infolistName);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertNull(
                $action,
                message: "Failed asserting that a infolist action with name [{$prettyName}] is not registered to [{$component->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionVisible(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionHidden(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionEnabled(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isDisabled(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDisabled(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->isEnabled(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionHasIcon(): Closure
    {
        return function (string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getIcon() === $icon,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDoesNotHaveIcon(): Closure
    {
        return function (string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getIcon() === $icon,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionHasLabel(): Closure
    {
        return function (string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getLabel() === $label,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDoesNotHaveLabel(): Closure
    {
        return function (string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getLabel() === $label,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionHasColor(): Closure
    {
        return function (string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertTrue(
                $action->getColor() === $color,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], has [{$colorName}] color on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDoesNotHaveColor(): Closure
    {
        return function (string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            $colorName = is_string($color) ? $color : 'custom';

            Assert::assertFalse(
                $action->getColor() === $color,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], does not have [{$colorName}] color on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionHasUrl(): Closure
    {
        return function (string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionDoesNotHaveUrl(): Closure
    {
        return function (string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            /** @phpstan-ignore-next-line */
            [$component, $action] = $this->getNestedInfolistActionComponentAndName($component, $name);

            $livewireClass = $this->instance()::class;
            $prettyName = implode(' > ', Arr::wrap($name));

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that a infolist action with name [{$prettyName}], registered to [{$component->getKey()}], should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertInfolistActionMounted(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            $name = $this->parseNestedActionName($name);

            $this->assertSet('mountedInfolistActions', $name);

            return $this;
        };
    }

    public function assertInfolistActionNotMounted(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): static {
            /** @phpstan-ignore-next-line */
            $this->assertInfolistActionExists($component, $name, $infolistName);

            $name = $this->parseNestedActionName($name);

            $this->assertNotSet('mountedInfolistActions', $name);

            return $this;
        };
    }

    public function assertInfolistActionHalted(): Closure
    {
        return $this->assertInfolistActionMounted();
    }

    public function assertHasInfolistActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.' . $value];
                        }

                        return ['mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function assertHasNoInfolistActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => 'mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.' . $value];
                        }

                        return ['mountedInfolistActionsData.' . array_key_last($this->instance()->mountedInfolistActionsData) . '.' . $key => $value];
                    })
                    ->all(),
            );

            return $this;
        };
    }

    public function getNestedInfolistActionComponentAndName(): Closure
    {
        return function (string $component, string | array $name, string $infolistName = 'infolist'): array {
            $name = $this->parseNestedActionName($name);

            $component = $this->instance()->getInfolist($infolistName)?->getComponent($component);

            return [$component, $component?->getAction($name)];
        };
    }
}
