<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Illuminate\Support\Arr;

trait HasFooterActions
{
    use HasFooterActionsAlignment;

    /**
     * @var array<Action | Closure>
     */
    protected array $footerActions = [];

    /**
     * @param  array<Action | Closure>  $actions
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
     * @return array<Action>
     */
    public function getFooterActions(): array
    {
        $actions = [];

        foreach ($this->footerActions as $footerAction) {
            foreach (Arr::wrap($this->evaluate($footerAction)) as $action) {
                $actions[] = $this->prepareAction($action);
            }
        }

        return $actions;
    }
}
