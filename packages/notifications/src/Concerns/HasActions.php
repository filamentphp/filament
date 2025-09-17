<?php

namespace Filament\Notifications\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\Size;
use Illuminate\Support\Arr;

trait HasActions
{
    /**
     * @var array<Action | ActionGroup> | ActionGroup | Closure
     */
    protected array | ActionGroup | Closure $actions = [];

    /**
     * @param  array<Action | ActionGroup> | ActionGroup | Closure  $actions
     */
    public function actions(array | ActionGroup | Closure $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getActions(): array
    {
        return array_map(
            fn (Action $action) => $action
                ->defaultView(Action::LINK_VIEW)
                ->defaultSize(Size::Small),
            Arr::wrap($this->evaluate($this->actions)),
        );
    }
}
