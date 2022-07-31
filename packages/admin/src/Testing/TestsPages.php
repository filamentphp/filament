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
class TestsPages
{
    public function fillForm(): Closure
    {
        return function (array $state = []): static {
            $this->set('data', $state);

            return $this;
        };
    }

    public function assertFormSet(): Closure
    {
        return function (array $state): static {
            foreach ($state as $key => $value) {
                $this->assertSet("data.{$key}", $value);
            }

            return $this;
        };
    }

    public function assertHasFormErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "data.{$value}"];
                        }

                        return ["data.{$key}" => $value];
                    })
                    ->toArray(),
            );

            return $this;
        };
    }

    public function assertHasNoFormErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoErrors(
                collect($keys)
                    ->mapWithKeys(function ($value, $key): array {
                        if (is_int($key)) {
                            return [$key => "data.{$value}"];
                        }

                        return ["data.{$key}" => $value];
                    })
                    ->toArray(),
            );

            return $this;
        };
    }

    public function callPageAction(): Closure
    {
        return function (string $name, array $data = [], array $arguments = []): static {
            $name = $this->parseActionName($name);

            /** @phpstan-ignore-next-line */
            $this->assertPageActionVisible($name);

            $this->call('mountAction', $name);

            $action = $this->instance()->getCachedAction($name);

            if (! $action->shouldOpenModal()) {
                $this->assertNotDispatchedBrowserEvent('open-modal');

                $this->assertNotSet('mountedAction', $name);

                return $this;
            }

            $this->assertSet('mountedAction', $name);

            $this->assertDispatchedBrowserEvent('open-modal', [
                'id' => 'page-action',
            ]);

            $this->set('mountedActionData', $data);

            $this->call('callMountedAction', json_encode($arguments));

            if ($this->get('mountedAction') !== $name) {
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
                    ->toArray(),
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
                    ->toArray(),
            );

            return $this;
        };
    }
}
