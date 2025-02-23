<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Illuminate\Support\Arr;

trait HasHeaderActions
{
    /**
     * @var array<Action | Closure>
     */
    protected array $headerActions = [];

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function headerActions(array $actions): static
    {
        $this->headerActions = [
            ...$this->headerActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array
    {
        $actions = [];

        foreach ($this->headerActions as $headerAction) {
            foreach (Arr::wrap($this->evaluate($headerAction)) as $action) {
                $actions[] = $this->prepareAction($action);
            }
        }

        return $actions;
    }
}
