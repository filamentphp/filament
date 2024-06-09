<?php

namespace Filament\Forms\Components\Builder;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Illuminate\Contracts\Support\Htmlable;

class Block extends Component
{
    use Concerns\HasName {
        getLabel as getDefaultLabel;
    }
    use Concerns\HasPreview;

    protected string | Closure | null $icon = null;

    protected int | Closure | null $maxItems = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function maxItems(int | Closure | null $maxItems): static
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function getLabel(?array $state = null, ?string $uuid = null): string | Htmlable
    {
        return $this->evaluate(
            $this->label,
            ['state' => $state, 'uuid' => $uuid],
        ) ?? $this->getDefaultLabel();
    }
}
