<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Actions\ActionGroup;
use Filament\Infolists\Components\Contracts\HasAffixActions;
use Filament\Infolists\Components\Contracts\HasFooterActions;
use Filament\Infolists\Components\Contracts\HasHeaderActions;
use Filament\Infolists\Components\Contracts\HasHintActions;
use Illuminate\Support\Arr;

trait HasActions
{
    /**
     * @var array<Action | ActionGroup> | null
     */
    protected ?array $cachedActions = null;

    /**
     * @var array<string, Action | ActionGroup | Closure>
     */
    protected array $actions = [];

    protected Action | ActionGroup | null $action = null;

    public function action(Action | ActionGroup | null $action): static
    {
        $this->action = $action;

        if ($action) {
            $this->registerActions([$action]);
        }

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup | Closure>  $actions
     */
    public function registerActions(array $actions): static
    {
        $this->actions = [
            ...$this->actions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @param  string | array<string> | null  $name
     */
    public function getAction(string | array | null $name = null): Action | ActionGroup | null
    {
        $actions = $this->cacheActions();

        if ($name === null) {
            return $this->action;
        }

        if (is_string($name) && str($name)->contains('.')) {
            $name = explode('.', $name);
        }

        if (is_array($name)) {
            $firstName = array_shift($name);
            $modalActionNames = $name;

            $name = $firstName;
        }

        $action = $actions[$name] ?? null;

        if (!$action) {
            return null;
        }

        foreach ($modalActionNames ?? [] as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (!$action) {
                return null;
            }

            $name = $modalActionName;
        }

        if (!$action instanceof Action) {
            return null;
        }

        return $action;
    }

    /**
     * @return array<string, Action | ActionGroup>
     */
    public function getActions(): array
    {
        return $this->cachedActions ??= $this->cacheActions();
    }

    /**
     * @return array<Action | ActionGroup>
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
                if ($action instanceof Action) {
                    $this->cachedActions[$action->getName()] = $this->prepareAction($action);
                }
            }
        }

        return $this->cachedActions;
    }

    public function prepareAction(Action $action): Action
    {
        return $action->component($this);
    }
}
