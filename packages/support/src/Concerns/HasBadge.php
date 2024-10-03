<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\IconPosition;

trait HasBadge
{
    protected string | int | float | Closure | null $badge = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    protected string | Closure | null $badgeIcon = null;

    protected IconPosition | string | Closure | null $badgeIconPosition = null;

    public function badge(string | int | float | Closure | null $badge = null): static
    {
        if (func_num_args() === 0) {
            /** @phpstan-ignore-next-line */
            return $this->view(static::BADGE_VIEW);
        }

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
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function badgeColor(string | array | Closure | null $color): static
    {
        $this->badgeColor = $color;

        return $this;
    }

    public function badgeIcon(string | Closure | null $icon): static
    {
        $this->badgeIcon = $icon;

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
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
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
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getBadgeColor(): string | array | null
    {
        return $this->evaluate($this->badgeColor);
    }

    public function getBadgeIcon(): ?string
    {
        return $this->evaluate($this->badgeIcon);
    }

    public function getBadgeIconPosition(): IconPosition | string
    {
        return $this->evaluate($this->badgeIconPosition) ?? IconPosition::Before;
    }
}
