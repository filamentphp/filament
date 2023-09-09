<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;

class Actions extends Component
{
    use HasAlignment;
    use HasVerticalAlignment;

    protected string $view = 'filament-infolists::components.actions';

    protected bool | Closure $isFullWidth = false;

    /**
     * @param  array<Action>  $actions
     */
    final public function __construct(array $actions)
    {
        $this->actions($actions);
    }

    /**
     * @param  array<Action>  $actions
     */
    public static function make(array $actions): static
    {
        $static = app(static::class, ['actions' => $actions]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<Action>  $actions
     */
    public function actions(array $actions): static
    {
        $this->childComponents(array_map(
            fn (Action $action): Component => $action->toInfolistComponent(),
            $actions,
        ));

        return $this;
    }

    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
    }
}
