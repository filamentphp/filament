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

    public function assertPageActionVisible(): Closure
    {
        return function (string $name): static {
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

    public function assertPageActionHeld(): Closure
    {
        return function (string $name): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionExists($name);

            $this->assertSet('mountedAction', $name);

            return $this;
        };
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
