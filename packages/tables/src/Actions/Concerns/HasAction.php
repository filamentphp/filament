<?php

namespace Filament\Tables\Actions\Concerns;

trait HasAction
{
    protected $action = null;

    public function action(string | callable | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string | callable | null
    {
        return $this->action;
    }
}
