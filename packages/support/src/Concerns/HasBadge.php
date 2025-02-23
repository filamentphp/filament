<?php

namespace Filament\Support\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Enums\IconPosition;

trait HasBadge
{
    protected string | int | float | Closure | null $badge = null;

    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    protected string | BackedEnum | Closure | null $badgeIcon = null;

    protected string | Closure | null $badgeTooltip = null;

    protected IconPosition | string | Closure | null $badgeIconPosition = null;

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
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function badgeColor(string | array | Closure | null $color): static
    {
        $this->badgeColor = $color;

        return $this;
    }

    public function badgeIcon(string | BackedEnum | Closure | null $icon): static
    {
        $this->badgeIcon = $icon;

        return $this;
    }

    public function badgeTooltip(string | Closure | null $tooltip): static
    {
        $this->badgeTooltip = $tooltip;

        return $this;
    }

    public function badgeIconPosition(IconPosition | string | Closure | null $position): static
    {
        $this->badgeIconPosition = $position;

        return $this;
    }

    /**
     * @deprecated Use `badgeColor()` instead.
     *
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function indicatorColor(string | array | Closure | null $color): static
    {
        return $this->badgeColor($color);
    }

    public function getBadge(): string | int | float | null
    {
        return $this->evaluate($this->badge);
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getBadgeColor(): string | array | null
    {
        return $this->evaluate($this->badgeColor);
    }

    public function getBadgeIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->badgeIcon);
    }

    public function getBadgeTooltip(): ?string
    {
        return $this->evaluate($this->badgeTooltip);
    }

    public function getBadgeIconPosition(): IconPosition | string
    {
        return $this->evaluate($this->badgeIconPosition) ?? IconPosition::Before;
    }
}
