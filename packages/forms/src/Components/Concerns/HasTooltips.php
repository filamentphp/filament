<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasTooltips
{
    /**
     * @var array<string | Htmlable> | Closure | null
     */
    protected array | Closure | null $tooltips = null;

    /**
     * @param  array<string | Htmlable> | Closure | null  $tooltips
     */
    public function tooltips(array | Closure | null $tooltips): static
    {
        $this->tooltips = $tooltips;

        return $this;
    }

    /**
     * @return string | Htmlable | array<string | Htmlable> | null
     */
    public function getTooltip(mixed $value): string | Htmlable | array | null
    {
        return $this->getTooltips()[$value] ?? null;
    }

    /**
     * @return ?array<string | Htmlable>
     */
    public function getTooltips(): ?array
    {
        return $this->evaluate($this->tooltips);
    }
}
