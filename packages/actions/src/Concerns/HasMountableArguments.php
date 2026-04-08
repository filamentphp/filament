<?php

namespace Filament\Actions\Concerns;

trait HasMountableArguments
{
    /**
     * @var array<string, mixed>|null
     */
    protected ?array $invokedArguments = null;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function __invoke(array $arguments): static
    {
        // Clone the action so that we don't accidentally mutate
        // the cached action's arguments while rendering it,
        // especially if it's mounted with different arguments.
        $action = clone $this;

        $action->arguments($arguments);
        $action->invokedArguments = $arguments;

        return $action;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInvokedArguments(): ?array
    {
        return $this->invokedArguments;
    }
}
