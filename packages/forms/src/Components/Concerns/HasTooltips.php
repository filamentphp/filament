<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasTooltips
{
    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $tooltips = null;

    /**
     * @param  array<string> | Closure | null  $tooltips
     */
    public function tooltips(array | Closure | null $tooltips): static
    {
        $this->tooltips = $tooltips;

        return $this;
    }

    /**
     * @return string | array<string> | null
     */
    public function getTooltip(mixed $value): string | array | null
    {
        return $this->getTooltips()[$value] ?? null;
    }

    /**
     * @return ?array<string>
     */
    public function getTooltips(): ?array
    {
        return $this->evaluate($this->tooltips);
    }
}
