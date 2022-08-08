<?php

namespace Filament\Testing;

use Closure;
use Filament\Pages\Page;
use Filament\Support\Testing\TestsActions;
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
                    ->all(),
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
                    ->all(),
            );

            return $this;
        };
    }
}
