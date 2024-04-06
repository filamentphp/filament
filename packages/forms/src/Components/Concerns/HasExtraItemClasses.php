<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasExtraItemClasses
{
    /**
     * @var null|Closure|string
     */
    protected null|Closure|string $extraItemClasses = null;

    /**
     * @param null|Closure|string $extraItemClasses
     * @return $this
     */
    public function extraItemClasses(null|Closure|string $extraItemClasses): static
    {
        $this->extraItemClasses = $extraItemClasses;
        return $this;
    }

    /**
     * @param array $arguments
     * @return ?array
     */
    public function getExtraItemClasses(array $arguments): ?string
    {
        if(! $this->extraItemClasses) {
            return null;
        }

        if (is_string($this->extraItemClasses)) {
            return $this->extraItemClasses;
        }

        $items = $this->getState();
        $data = [];

        if (isset($items[$arguments['item']])) {
            $data = $items[$arguments['item']];
        }

        return $this->evaluate($this->extraItemClasses, [
            'data' => $data,
        ]);
    }
}
