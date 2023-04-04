<?php

namespace Filament\Testing;

use Closure;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Testing\TestsActions;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method Page instance()
 *
 * @mixin TestableLivewire
 * @mixin TestsActions
 */
class TestsPageActions
{
    public function mountPageAction(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionVisible($name);

            $this->call('mountAction', $name);

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            $action = $this->instance()->getCachedAction($name);

            if (! $action->shouldOpenModal()) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                $this->assertNotSet('mountedAction', $action->getName());

                return $this;
            }

            $this->assertSet('mountedAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => 'page-action',
            ]);

            return $this;
        };
    }

    public function setPageActionData(): Closure
    {
        return function (array $data): static {
            $this->set('mountedActionData', $data);

            return $this;
        };
    }

    public function assertPageActionDataSet(): Closure
    {
        return function (array $data): static {
            foreach ($data as $key => $value) {
                $this->assertSet("mountedActionData.{$key}", $value);
            }

            return $this;
        };
    }

    public function callPageAction(): Closure
    {
        return function (string $name, array $data = [], array $arguments = []): static {
            /** @phpstan-ignore-next-line */
            $this->mountPageAction($name);

            if (! $this->instance()->getMountedAction()) {
                return $this;
            }

            /** @phpstan-ignore-next-line */
            $this->setPageActionData($data);

            /** @phpstan-ignore-next-line */
            $this->callMountedPageAction($arguments);

            return $this;
        };
    }

    public function callMountedPageAction(): Closure
    {
        return function (array $arguments = []): static {
            $action = $this->instance()->getMountedAction();

            if (! $action) {
                return $this;
            }

            $this->call('callMountedAction', json_encode($arguments));

            if (filled($this->instance()->redirectTo)) {
                return $this;
            }

            if ($this->get('mountedAction') !== $action->getName()) {
                $this->assertDispatchedBrowserEvent('close-modal', [
                    'id' => 'page-action',
                ]);
            }

            return $this;
        };
    }

    public function assertPageActionExists(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertInstanceOf(
                Action::class,
                $action,
                message: "Failed asserting that an action with name [{$name}] exists on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertPageActionDoesNotExist(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertNull(
                $action,
                message: "Failed asserting that an action with name [{$name}] does not exist on the [{$livewireClass}] page.",
            );

            return $this;
        };
    }

    public function assertPageActionsExistInOrder(): Closure
    {
        return function (array $names): static {
            $livewire = $this->instance();
            $this->assertActionListInOrder(
                $names,
                $livewire->getCachedActions(),
                'page',
                Action::class,
            );

            return $this;
        };
    }

    public function assertPageActionVisible(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$name}] is visible on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHidden(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->isHidden(),
                message: "Failed asserting that an action with name [{$name}] is hidden on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionEnabled(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->isEnabled(),
                message: "Failed asserting that an action with name [{$name}] is enabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionDisabled(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->isDisabled(),
                message: "Failed asserting that an action with name [{$name}] is disabled on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHasIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$name}] has icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionDoesNotHaveIcon(): Closure
    {
        return function (string $name, string $icon, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->getIcon() === $icon,
                message: "Failed asserting that an action with name [{$name}] does not have icon [{$icon}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHasLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$name}] has label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionDoesNotHaveLabel(): Closure
    {
        return function (string $name, string $label, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->getLabel() === $label,
                message: "Failed asserting that an action with name [{$name}] does not have label [{$label}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHasColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$name}] has color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionDoesNotHaveColor(): Closure
    {
        return function (string $name, string $color, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->getColor() === $color,
                message: "Failed asserting that an action with name [{$name}] does not have color [{$color}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHasUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$name}] has URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionDoesNotHaveUrl(): Closure
    {
        return function (string $name, string $url, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->getUrl() === $url,
                message: "Failed asserting that an action with name [{$name}] does not have URL [{$url}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionShouldOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertTrue(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$name}] should open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionShouldNotOpenUrlInNewTab(): Closure
    {
        return function (string $name, $record = null): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            $action = $livewire->getCachedAction($name);

            Assert::assertFalse(
                $action->shouldOpenUrlInNewTab(),
                message: "Failed asserting that an action with name [{$name}] should not open url in new tab on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertPageActionHalted(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $this->assertSet('mountedAction', $name);

            return $this;
        };
    }

    /**
     * @deprecated Use `->assertPageActionHalted()` instead.
     */
    public function assertPageActionHeld(): Closure
    {
        return $this->assertPageActionHalted();
    }

    public function assertHasPageActionErrors(): Closure
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

    public function assertHasNoPageActionErrors(): Closure
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
}
