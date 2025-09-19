<?php

namespace Filament\Navigation;

use BackedEnum;
use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasBadgeTooltip;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use LogicException;
use UnitEnum;

class NavigationItem extends Component
{
    use HasBadgeTooltip;

    protected string | UnitEnum | Closure | null $group = null;

    protected string | Closure | null $parentItem = null;

    protected bool | Closure | null $isActive = null;

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

    protected string | BackedEnum | Htmlable | Closure | null $activeIcon = null;

    protected string | Closure $label;

    protected string | Closure | null $badge = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $badgeColor = null;

    protected bool | Closure $shouldOpenUrlInNewTab = false;

    protected int | Closure | null $sort = null;

    protected string | Closure | null $url = null;

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    /**
     * @var array<NavigationItem> | Arrayable
     */
    protected array | Arrayable $childItems = [];

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
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function badge(string | Closure | null $badge, string | array | Closure | null $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;

        return $this;
    }

    public function group(string | UnitEnum | Closure | null $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function parentItem(string | Closure | null $group): static
    {
        $this->parentItem = $group;

        return $this;
    }

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
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

    public function activeIcon(string | BackedEnum | Htmlable | Closure | null $activeIcon): static
    {
        $this->activeIcon = $activeIcon;

        return $this;
    }

    public function isActiveWhen(?Closure $callback): static
    {
        $this->isActive = $callback;

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

    public function url(string | Closure | null $url, bool | Closure | null $shouldOpenInNewTab = null): static
    {
        $this->url = $url;

        if ($shouldOpenInNewTab !== null) {
            $this->openUrlInNewTab($shouldOpenInNewTab);
        }

        return $this;
    }

    public function getBadge(): ?string
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

    public function getGroup(): string | UnitEnum | null
    {
        return $this->evaluate($this->group);
    }

    public function getParentItem(): ?string
    {
        return $this->evaluate($this->parentItem);
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        $icon = $this->evaluate($this->icon);

        if (blank($icon) && $this->getChildItems()) {
            throw new LogicException("Navigation item [{$this->getLabel()}] has child items but no icon. Parent items must have an icon to ensure a proper user experience.");
        }

        return $icon;
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

    public function getActiveIcon(): string | BackedEnum | Htmlable | null
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
        if ($this->isActive instanceof Closure) {
            $this->isActive = ((bool) $this->evaluate($this->isActive));
        }

        return (bool) $this->isActive;
    }

    public function isChildItemsActive(): bool
    {
        foreach ($this->getChildItems() as $childItem) {
            if ($childItem->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }

    /**
     * @param  array<NavigationItem> | Arrayable  $items
     */
    public function childItems(array | Arrayable $items): static
    {
        $this->childItems = $items;

        return $this;
    }

    /**
     * @return array<NavigationItem> | Arrayable
     */
    public function getChildItems(): array | Arrayable
    {
        return $this->childItems;
    }
}
