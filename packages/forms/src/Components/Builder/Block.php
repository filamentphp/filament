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

    protected string $view = 'forms::components.builder.block';

    protected string | Closure | null $icon = null;

    protected ?array $labelState = null;

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

    public function labelState(?array $state): static
    {
        $this->labelState = $state;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getLabel(): string | Htmlable
    {
        return $this->evaluate(
            $this->label,
            $this->labelState ? ['state' => $this->labelState] : [],
        ) ?? $this->getDefaultLabel();
    }
}
