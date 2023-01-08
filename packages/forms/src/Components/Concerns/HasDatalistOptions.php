<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

trait HasDatalistOptions
{
    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $datalistOptions = null;

    /**
     * @param  array<string> | Arrayable | Closure | null  $options
     */
    public function datalist(array | Arrayable | Closure | null $options): static
    {
        $this->datalistOptions = $options;

        return $this;
    }

    /**
     * @return array<string> | null
     */
    public function getDatalistOptions(): ?array
    {
        $options = $this->evaluate($this->datalistOptions);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }
}
