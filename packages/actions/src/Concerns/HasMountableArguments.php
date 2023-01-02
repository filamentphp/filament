<?php

namespace Filament\Actions\Concerns;

trait HasMountableArguments
{
    /**
     * @param  array<string, mixed>  $arguments
     */
    public function __invoke(array $arguments): static
    {
        $this->arguments($arguments);

        return $this;
    }
}
