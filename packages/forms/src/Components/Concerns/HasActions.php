<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Contracts\HasAffixActions;
use Filament\Forms\Components\Contracts\HasExtraItemActions;
use Filament\Forms\Components\Contracts\HasFooterActions;
use Filament\Forms\Components\Contracts\HasHeaderActions;
use Filament\Forms\Components\Contracts\HasHintActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasActions
{
    /**
     * @var array<Action> | null
     */
    protected ?array $cachedActions = null;

    /**
     * @var array<string, Action | Closure>
     */
    protected array $actions = [];

    protected Model | string | null $actionFormModel = null;

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function registerActions(array $actions): static
    {
        $this->actions = [
            ...$this->actions,
            ...$actions,
        ];

        return $this;
    }

    public function getAction(string $name): ?Action
    {
        return $this->getActions()[$name] ?? null;
    }

    /**
     * @return array<string, Action>
     */
    public function getActions(): array
    {
        return $this->cachedActions ??= $this->cacheActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheActions(): array
    {
        $this->cachedActions = [];

        if ($this instanceof HasAffixActions) {
            $this->cachedActions = [
                ...$this->cachedActions,
                ...$this->getPrefixActions(),
                ...$this->getSuffixActions(),
            ];
        }

        if ($this instanceof HasExtraItemActions) {
            $this->cachedActions = [
                ...$this->cachedActions,
                ...$this->getExtraItemActions(),
            ];
        }

        if ($this instanceof HasFooterActions) {
            $this->cachedActions = [
                ...$this->cachedActions,
                ...$this->getFooterActions(),
            ];
        }

        if ($this instanceof HasHeaderActions) {
            $this->cachedActions = [
                ...$this->cachedActions,
                ...$this->getHeaderActions(),
            ];
        }

        if ($this instanceof HasHintActions) {
            $this->cachedActions = [
                ...$this->cachedActions,
                ...$this->getHintActions(),
            ];
        }

        foreach ($this->actions as $registeredAction) {
            foreach (Arr::wrap($this->evaluate($registeredAction)) as $action) {
                $this->cachedActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedActions;
    }

    public function prepareAction(Action $action): Action
    {
        return $action->component($this);
    }

    public function actionFormModel(Model | string | null $model): static
    {
        $this->actionFormModel = $model;

        return $this;
    }

    public function getActionFormModel(): Model | string | null
    {
        return $this->actionFormModel ?? $this->getRecord() ?? $this->getModel();
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->getActions());
    }
}
