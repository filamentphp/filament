<?php

namespace Filament\Forms\Components\Builder;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Illuminate\Support\Str;

class Block extends Component
{
    use Concerns\HasName;

    protected string $view = 'forms::components.builder.block';

    protected $icon = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function icon(string | callable $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getLabel(): string
    {
        return parent::getLabel() ?? (string) Str::of($this->getName())
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
    }
}
