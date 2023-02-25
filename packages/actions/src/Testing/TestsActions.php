<?php

namespace Filament\Actions\Testing;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\MountableAction;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasActions instance()
 *
 * @mixin TestableLivewire
 */
class TestsActions
{
    public function mountAction(): Closure
    {
        return function (string $name, array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionVisible($name);

            $this->call('mountAction', $name, $arguments);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->instance()->mountedAction === null) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                return $this;
            }

            $this->assertSet('mountedAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => "{$this->instance()->id}-action",
            ]);

            return $this;
        };
    }

    public function setActionData(): Closure
    {
        return function (array $data): static {
            foreach (Arr::prependKeysWith($data, 'mountedActionData.') as $key => $value) {
                $this->set($key, $value);
            }

            return $this;
        };
    }

    public function assertActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach ($data as $key => $value) {
                $this->assertSet("mountedActionData.{$key}", $value);
            }

            return $this;
        };
    }

    public function callAction(): Closure
    {
        return function (string $name, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->mountAction($name, $arguments);

            if (! $this->instance()->getMountedAction()) {
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

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->get('mountedAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => "{$this->instance()->id}-action",
                ]);
            }

            return $this;
        };
    }

    public function assertActionExists(): Closure
    {
        return function (string $name): static {
            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that an action with name [{$name}] exists on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotExist(): Closure
    {
        return function (string $name): static {
            try {
                $action = $this->instance()->getAction($name);
            } catch (Exception $exception) {
                $action = null;
            }

            $livewireClass = $this->instance()::class;

            Assert::assertNull(
                $action,
                message: "Failed asserting that an action with name [{$name}] does not exist on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertActionVisible(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHidden(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionEnabled(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->isEnabled(),
                message: "Failed asserting that an action with name [{$name}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDisabled(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->isDisabled(),
                message: "Failed asserting that an action with name [{$name}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$name}] has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$name}] does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$name}] has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$name}] does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$name}] has color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHasUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$name}] has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionDoesNotHaveUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$name}] does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$name}] should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $action = $this->instance()->getAction($name);

            $livewireClass = $this->instance()::class;

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$name}] should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertActionHalted(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertActionExists($name);

            $this->assertSet('mountedAction', $name);

            return $this;
        };
    }

    /**
     * @deprecated Use `->assertActionHalted()` instead.
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
                            return [$key => "mountedActionData.{$value}"];
                        }

                        return ["mountedActionData.{$key}" => $value];
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
                            return [$key => "mountedActionData.{$value}"];
                        }

                        return ["mountedActionData.{$key}" => $value];
                    })
                    ->all(),
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
}
