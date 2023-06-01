<?php

namespace Filament\Actions\Concerns;

trait HasMountableArguments
{
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

        return $action;
    }
}
