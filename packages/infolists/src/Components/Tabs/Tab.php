<?php

namespace Filament\Infolists\Components\Tabs;

use Closure;
use Filament\Infolists\Components\Component;
use Illuminate\Support\Str;

class Tab extends Component
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.tabs.tab';

    protected string | Closure | null $badge = null;

    protected string | Closure | null $icon = null;

    protected string | Closure | null $iconPosition = null;

    protected string | Closure | null $iconColor = null;

    final public function __construct(string $label)
    {
        $this->label($label);
        $this->id(Str::slug($label));
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconPosition(string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function iconColor(string | Closure | null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    public function badge(string | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function getId(): string
    {
        return $this->getContainer()->getParentComponent()->getId() . '-' . parent::getId() . '-tab';
    }

    /**
     * @return array<string, int | null>
     */
    public function getColumnsConfig(): array
    {
        return $this->columns ?? $this->getContainer()->getColumnsConfig();
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getIconPosition(): ?string
    {
        return $this->evaluate($this->iconPosition) ?? 'before';
    }

    public function getIconColor(): ?string
    {
        return $this->evaluate($this->iconColor);
    }

    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }
}
