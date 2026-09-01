<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasArguments
{
    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $arguments = [];

    /**
     * @param  array<mixed> | Closure  $arguments
     */
    public function arguments(array | Closure $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getArguments(): array
    {
        return $this->evaluate($this->arguments) ?? [];
    }
}
