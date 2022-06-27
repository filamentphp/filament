<?php

namespace Filament\Forms\Components\Wizard;

use Closure;
use Filament\Forms\Components\Component;
use Illuminate\Support\Str;

class Step extends Component
{
    protected ?Closure $afterValidated = null;

    protected string | Closure | null $description = null;

    protected string | Closure | null $icon = null;

    protected string $view = 'forms::components.wizard.step';

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

    public function afterValidated(?Closure $callback): static
    {
        $this->afterValidated = $callback;

        return $this;
    }

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function callAfterValidated(): void
    {
        $this->evaluate($this->afterValidated);
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }

    public function getColumnsConfig(): array
    {
        return $this->columns ?? $this->getContainer()->getColumnsConfig();
    }
}
