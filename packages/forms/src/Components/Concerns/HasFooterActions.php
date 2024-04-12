<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Concerns\HasFooterActionsAlignment;
use Illuminate\Support\Arr;

trait HasFooterActions
{
    use HasFooterActionsAlignment;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedFooterActions = null;

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
        return $this->cachedFooterActions ?? $this->cacheFooterActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheFooterActions(): array
    {
        $this->cachedFooterActions = [];

        foreach ($this->footerActions as $footerAction) {
            foreach (Arr::wrap($this->evaluate($footerAction)) as $action) {
                $this->cachedFooterActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedFooterActions;
    }
}
