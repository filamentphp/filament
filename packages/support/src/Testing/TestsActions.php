<?php

namespace Filament\Support\Testing;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
}
