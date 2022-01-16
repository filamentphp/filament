<?php

namespace Filament\Pages\Actions\Modal\Actions\Concerns;

trait HasAction
{
    protected ?string $action = null;

    public function action(string | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string | null
    {
        return $this->action;
    }
}
