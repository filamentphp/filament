<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasExtraItemClasses
{
    /**
     * @var ?Closure
     */
    protected ?Closure $extraItemClasses = null;

    /**
     * @param Closure $extraItemClasses
     * @return $this
     */
    public function extraItemClasses(Closure $extraItemClasses): static
    {
        $this->extraItemClasses = $extraItemClasses;
        return $this;
    }

    /**
     * @param array $arguments
     * @return ?array
     */
    public function getExtraItemClasses(array $arguments): ?array
    {
        return $this->evaluate($this->extraItemClasses, [
            'arguments' => $arguments,
        ]);
    }
}
