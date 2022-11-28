<?php

namespace Filament\Actions\Concerns;

trait HasArguments
{
    /**
     * @var array<string, mixed>
     */
    protected array $arguments = [];

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function arguments(array $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function resetArguments(): static
    {
        $this->arguments([]);

        return $this;
    }
}
