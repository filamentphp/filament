<?php

namespace Filament\Support\Testing;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
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
}
