<?php

namespace Filament\Actions\Concerns;

trait HasArguments
{
    /**
     * @var array<string, mixed> | null
     */
    protected ?array $arguments = null;

    /**
     * @param  array<string, mixed> | null  $arguments
     */
    public function arguments(?array $arguments): static
    {
        if ($arguments === null) {
            $this->arguments = null;

            return $this;
        }

        $this->arguments = [];
        $this->mergeArguments($arguments);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mergeArguments(array $arguments): static
    {
        $this->arguments = [
            ...$this->arguments ?? [],
            ...$arguments,
        ];

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        $arguments = $this->arguments ?? [];

        $nestingIndex = $this->getNestingIndex();

        if (blank($nestingIndex)) {
            return $arguments;
        }

        return [
            ...$this->getLivewire()->mountedActions[$nestingIndex]['arguments'] ?? [],
            ...$arguments,
        ];
    }

    public function resetArguments(): static
    {
        $this->arguments(null);

        return $this;
    }

    public function hasArguments(): bool
    {
        return $this->arguments !== null;
    }
}
