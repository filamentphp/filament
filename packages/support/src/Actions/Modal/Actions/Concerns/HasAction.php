<?php

namespace Filament\Support\Actions\Modal\Actions\Concerns;

trait HasAction
{
    protected ?string $action = null;

    protected ?array $actionArguments = null;

    public function action(?string $action, ?array $arguments = []): static
    {
        $this->action = $action;
        $this->actionArguments = $arguments;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getActionArguments(): ?array
    {
        return $this->actionArguments;
    }
}
