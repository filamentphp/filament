<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;

trait HasExtraItemActions
{
    /**
     * @var array<Action | Closure>
     */
    protected array $extraItemActions = [];

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedExtraItemActions = null;

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function extraItemActions(array $actions): static
    {
        $this->extraItemActions = [
            ...$this->extraItemActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array
    {
        return $this->cachedExtraItemActions ?? $this->cacheExtraItemActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheExtraItemActions(): array
    {
        $this->cachedExtraItemActions = [];

        foreach ($this->extraItemActions as $extraItemAction) {
            foreach (Arr::wrap($this->evaluate($extraItemAction)) as $action) {
                $this->cachedExtraItemActions[$action->getName()] = $this->prepareAction(
                    $action
                        ->defaultColor('gray')
                        ->defaultSize(ActionSize::Small)
                        ->defaultView(Action::ICON_BUTTON_VIEW),
                );
            }
        }

        return $this->cachedExtraItemActions;
    }
}
