<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanCustomizeProcess
{
    protected ?Closure $using = null;

    public function using(?Closure $using): static
    {
        $this->using = $using;

        return $this;
    }

    /**
     * @param array<string, mixed> $parameters
     * @return mixed
     */
    public function process(?Closure $default, array $parameters = [])
    {
        return $this->evaluate($this->using ?? $default, $parameters);
    }
}
