<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

trait CanCallAction
{
    protected Closure | Action | string | null $action = null;

    public function action(Closure | Action | string | null $action): static
    {
        if (is_string($action)) {
            $action = function (HasTable $livewire, ?Model $record) use ($action) {
                if ($record) {
                    return $livewire->{$action}($record);
                }

                return $livewire->{$action}();
            };
        }

        $this->action = $action;

        return $this;
    }

    public function getAction(): Closure | Action | null
    {
        return $this->action;
    }
}
