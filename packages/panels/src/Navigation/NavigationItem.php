<?php

namespace Filament\Navigation;

use Closure;
use Filament\Support\Components\Component;

class NavigationItem extends Component
{
    protected string | Closure | null $group = null;

    protected ?Closure $isActiveWhen = null;

    protected string | Closure | null $icon = null;

    protected string | Closure | null $activeIcon = null;

    protected string | Closure $label;

    protected string | Closure | null $badge = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    protected bool | Closure $shouldOpenUrlInNewTab = false;

    protected int | Closure | null $sort = null;

    protected string | Closure | null $url = null;

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    final public function __construct(string | Closure | null $label = null)
    {
        if (filled($label)) {
            $this->label($label);
        }
    }

    public static function make(string | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function badge(string | Closure | null $badge, string | array | Closure | null $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;

        return $this;
    }

    public function group(string | Closure | null $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function activeIcon(string | Closure | null $activeIcon): static
    {
        $this->activeIcon = $activeIcon;

        return $this;
    }

    public function isActiveWhen(Closure $callback): static
    {
        $this->isActiveWhen = $callback;

        return $this;
    }

    public function label(string | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function openUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function sort(int | Closure | null $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function url(string | Closure | null $url, bool | Closure $shouldOpenInNewTab = false): static
    {
        $this->openUrlInNewTab($shouldOpenInNewTab);
        $this->url = $url;

        return $this;
    }

    public function getBadge(): ?string
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

    public function getGroup(): ?string
    {
        return $this->evaluate($this->group);
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        return ! $this->evaluate($this->isVisible);
    }

    public function getActiveIcon(): ?string
    {
        return $this->evaluate($this->activeIcon);
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label);
    }

    public function getSort(): int
    {
        return $this->evaluate($this->sort) ?? -1;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function isActive(): bool
    {
        $callback = $this->isActiveWhen;

        if ($callback === null) {
            return false;
        }

        return (bool) $this->evaluate($callback);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }
}
