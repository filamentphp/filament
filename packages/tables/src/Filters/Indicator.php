<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasColor;
use Illuminate\Contracts\Support\Htmlable;

class Indicator extends Component
{
    use HasColor;

    protected bool | Closure $isRemovable = true;

    protected string | Htmlable | Closure $label;

    protected string | Closure | null $removeField = null;

    protected string | Closure | null $removeLivewireClickHandler = null;

    protected string $evaluationIdentifier = 'indicator';

    final public function __construct(string | Htmlable | Closure $label)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure $label): static
    {
        return app(static::class, ['label' => $label]);
    }

    public function label(string | Htmlable | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string | Htmlable
    {
        return $this->evaluate($this->label);
    }

    public function removable(bool | Closure $condition = true): static
    {
        $this->isRemovable = $condition;

        return $this;
    }

    public function isRemovable(): bool
    {
        return (bool) $this->evaluate($this->isRemovable);
    }

    public function removeField(string | Closure | null $name): static
    {
        $this->removeField = $name;

        return $this;
    }

    public function getRemoveField(): ?string
    {
        return $this->evaluate($this->removeField);
    }

    public function removeLivewireClickHandler(string | Closure | null $handler): static
    {
        $this->removeLivewireClickHandler = $handler;

        return $this;
    }

    public function getRemoveLivewireClickHandler(): ?string
    {
        return $this->evaluate($this->removeLivewireClickHandler);
    }
}
