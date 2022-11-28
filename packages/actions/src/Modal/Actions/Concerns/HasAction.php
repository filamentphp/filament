<?php

namespace Filament\Actions\Modal\Actions\Concerns;

trait HasAction
{
    protected ?string $action = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $actionArguments = null;

    /**
     * @param  array<string, mixed> | null  $arguments
     */
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

    /**
     * @return array<string, mixed> | null
     */
    public function getActionArguments(): ?array
    {
        return $this->actionArguments;
    }
}
