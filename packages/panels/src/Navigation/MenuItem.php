<?php

namespace Filament\Navigation;

use Closure;
use Filament\Forms\Components\Concerns\CanBeHidden;
use Filament\Forms\Components\Concerns\HasLabel;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Laravel\SerializableClosure\Serializers\Native;

class MenuItem extends Component
{
    use HasColor;
    use HasIcon;
    use HasLabel;
    use CanBeHidden;

    protected string | Closure | null $postAction = null;

    protected int | Closure | null $sort = null;

    protected string | Closure | Native | null $url = null;

    protected bool | Closure $shouldOpenUrlInNewTab = false;

    final public function __construct() {}

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
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

    public function getPostAction(): ?string
    {
        return $this->evaluate($this->postAction);
    }

    public function getSort(): int
    {
        return $this->evaluate($this->sort) ?? -1;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }
}
