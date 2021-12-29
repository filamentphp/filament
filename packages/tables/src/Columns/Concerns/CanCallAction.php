<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

trait CanCallAction
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

    public function callAction()
    {
        $action = $this->getAction();

        if (! $action) {
            return;
        }

        return app()->call($action, [
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ]);
    }

    public function getAction(): ?Closure
    {
        return $this->action;
    }
}
