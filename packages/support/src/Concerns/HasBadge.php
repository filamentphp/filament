<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasBadge
{
    protected string | int | float | Closure | null $badge = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    public function badge(string | int | float | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @deprecated Use `badge()` instead.
     */
    public function indicator(string | int | float | Closure | null $indicator): static
    {
        return $this->badge($indicator);
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function badgeColor(string | array | Closure | null $color): static
    {
        $this->badgeColor = $color;

        return $this;
    }

    /**
     * @deprecated Use `badgeColor()` instead.
     */
    public function indicatorColor(string | Closure | null $color): static
    {
        return $this->badgeColor($color);
    }

    public function getBadge(): string | int | float | null
    {
        return $this->evaluate($this->badge);
    }

    /**
     * @return string | array<string> | null
     */
    public function getBadgeColor(): string | array | null
    {
        return $this->evaluate($this->badgeColor);
    }
}
