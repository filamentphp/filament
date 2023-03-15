<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;

class Actions extends Component
{
    protected string $view = 'filament-forms::components.actions';

    protected string | Closure | null $alignment = null;

    protected bool | Closure $isFullWidth = false;

    protected string | Closure | null $verticalAlignment = null;

    final public function __construct(array $actions)
    {
        $this->actions($actions);
    }

    public static function make(array $actions): static
    {
        return app(static::class, ['actions' => $actions]);
    }

    public function actions(array $actions): static
    {
        $this->childComponents(array_map(
            fn (Action $action): Component => $action->toFormComponent(),
            $actions,
        ));

        return $this;
    }

    public function alignment(string | Closure | null $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function verticalAlignment(string | Closure | null $alignment): static
    {
        $this->verticalAlignment = $alignment;

        return $this;
    }

    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function getAlignment(): ?string
    {
        return $this->evaluate($this->alignment);
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
    }

    public function getVerticalAlignment(): ?string
    {
        return $this->evaluate($this->verticalAlignment);
    }
}
