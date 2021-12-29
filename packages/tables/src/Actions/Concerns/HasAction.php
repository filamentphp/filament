<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

trait HasAction
{
    protected ?Closure $action = null;

    public function action(string | Closure | null $action): static
    {
        if (is_string($action)) {
            $action = function (HasTable $livewire, Model $record) use ($action) {
                return $livewire->{$action}($record);
            };
        }

        $this->action = $action;

        return $this;
    }

    public function getAction(): ?Closure
    {
        return $this->action;
    }
}
