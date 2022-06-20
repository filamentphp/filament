<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait CanCustomizeProcess
{
    protected ?Closure $using = null;

    public function using(?Closure $using): static
    {
        $this->using = $using;

        return $this;
    }

    public function process(?Closure $default, array $parameters = [])
    {
        return $this->evaluate($this->using ?? $default, $parameters);
    }
}
