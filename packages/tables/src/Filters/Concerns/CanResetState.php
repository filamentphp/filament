<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait CanResetState
{
    /**
     * @var array<string, mixed>
     */
    protected array | Closure | null $resetState = null;

    /**
     * @param  array<string, mixed> | Closure | null  $state
     */
    public function resetState(array | Closure | null $state): static
    {
        $this->resetState = $state;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResetState(): array
    {
        return $this->evaluate($this->resetState) ?? [];
    }
}
