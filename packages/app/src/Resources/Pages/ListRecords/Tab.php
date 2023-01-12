<?php

namespace Filament\Resources\Pages\ListRecords;

use Closure;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Builder;

class Tab extends Component
{
    protected string | Closure | null $label = null;

    protected string | Closure | null $icon = null;

    protected string | Closure | null $badge = null;

    protected ?Closure $modifyQueryUsing = null;

    public function __construct(string | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    public function badge(string | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function query(Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $this->evaluate($this->modifyQueryUsing, [
            'query' => $query,
        ]) ?? $query;
    }
}
