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
     * @var ?array<string | Htmlable>
     */
    protected ?array $cachedTooltips = null;

    protected bool $hasCachedTooltips = false;

    /**
     * @param  array<string | Htmlable> | Closure | null  $tooltips
     */
    public function tooltips(array | Closure | null $tooltips): static
    {
        $this->tooltips = $tooltips;

        $this->cachedTooltips = null;
        $this->hasCachedTooltips = false;

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
        if ($this->hasCachedTooltips) {
            return $this->cachedTooltips;
        }

        $this->hasCachedTooltips = true;

        return $this->cachedTooltips = $this->evaluate($this->tooltips);
    }
}
