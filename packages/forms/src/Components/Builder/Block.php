<?php

namespace Filament\Forms\Components\Builder;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Illuminate\Support\Str;

class Block extends Component
{
    use Concerns\HasName;

    protected string $view = 'forms::components.builder.block';

    protected string | Closure | null $icon = null;

    protected ?array $labelState = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
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

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label, array_merge(
            $this->labelState ? ['state' => $this->labelState] : [],
        ));

        return $label ?? (string) Str::of($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }
}
