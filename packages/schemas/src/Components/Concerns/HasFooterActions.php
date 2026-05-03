<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Arr;

trait HasFooterActions
{
    use HasFooterActionsAlignment;

    /**
     * @var array<Action | ActionGroup | Closure>
     */
    protected array $footerActions = [];

    /**
     * @param  array<Action | ActionGroup | Closure>  $actions
     */
    public function footerActions(array $actions): static
    {
        $this->footerActions = [
            ...$this->footerActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFooterActions(): array
    {
        $actions = [];

        foreach ($this->footerActions as $footerAction) {
            foreach (Arr::wrap($this->evaluate($footerAction)) as $action) {
                $actions[] = ($action instanceof ActionGroup)
                    ? $this->prepareActionGroup($action)
                    : $this->prepareAction($action);
            }
        }

        return $actions;
    }
}
