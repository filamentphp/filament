<?php

namespace Filament\Forms\Components\Builder;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;

class Block extends Component
{
    use Concerns\HasName;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.builder.block';

    protected string | Closure | null $icon = null;

    /**
     * @var array<string, mixed> | null
     */
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

    /**
     * @param  array<string, mixed> | null  $state
     */
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

        return $label ?? (string) str($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }
}
