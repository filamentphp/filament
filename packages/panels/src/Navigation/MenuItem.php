<?php

namespace Filament\Navigation;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Support\Components\Component;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\Serializers\Native;

/**
 * @deprecated Use `Filament\Actions\Action` instead.
 */
class MenuItem extends Component
{
    protected string | Closure | null $color = null;

    protected string | BackedEnum | Closure | null $icon = null;

    protected string | Closure | null $label = null;

    protected string | Closure | null $postAction = null;

    protected int | Closure | null $sort = null;

    protected string | Closure | Native | null $url = null;

    protected bool | Closure $shouldOpenUrlInNewTab = false;

    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    final public function __construct() {}

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function color(string | Closure | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function icon(string | BackedEnum | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function postAction(string | Closure | null $action): static
    {
        $this->postAction = $action;

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

    public function openUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
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

    public function getColor(): ?string
    {
        return $this->evaluate($this->color);
    }

    public function getIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->icon);
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    public function getPostAction(): ?string
    {
        return $this->evaluate($this->postAction);
    }

    public function getSort(): int
    {
        return $this->evaluate($this->sort) ?? 0;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }

    public function toAction(?Action $action = null): Action
    {
        $label = $this->getLabel();
        $postAction = $this->getPostAction();

        return ($action ?? Action::make(Str::slug(Str::transliterate($label, strict: true))))
            ->color($this->getColor())
            ->icon($this->getIcon())
            ->label($label)
            ->postToUrl(filled($postAction))
            ->sort($this->getSort())
            ->visible($this->isVisible())
            ->url($this->getUrl() ?? $postAction, $this->shouldOpenUrlInNewTab());
    }
}
