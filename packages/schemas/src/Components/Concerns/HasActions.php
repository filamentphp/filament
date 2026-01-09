<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Schemas\Components\Contracts\HasExtraItemActions;
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

    /**
     * @var Model|class-string<Model>|null
     */
    protected Model | string | Closure | null $actionSchemaModel = null;

    protected ?Action $action = null;

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

    public function action(?Action $action): static
    {
        $this->action = $action;

        if ($action) {
            $this->registerActions([$action]);
        }

        return $this;
    }

    /**
     * @param  string | array<string> | null  $name
     */
    public function getAction(string | array | null $name = null): ?Action
    {
        $actions = $this->getActions();

        if (blank($name)) {
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

        if (! $action) {
            return null;
        }

        foreach ($modalActionNames ?? [] as $modalActionName) {
            $action = $action->getModalAction($modalActionName);

            if (! $action) {
                return null;
            }

            $name = $modalActionName;
        }

        return $action;
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

        foreach ($this->getDefaultActions() as $defaultAction) {
            foreach (Arr::wrap($this->evaluate($defaultAction)) as $action) {
                $this->cachedActions[$action->getName()] = $this->prepareAction($action);
            }
        }

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

        foreach ($this->actions as $registeredAction) {
            foreach (Arr::wrap($this->evaluate($registeredAction)) as $action) {
                $this->cachedActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedActions;
    }

    /**
     * @return array<Action>
     */
    public function getDefaultActions(): array
    {
        return [];
    }

    public function prepareAction(Action $action): Action
    {
        return $action->schemaComponent($this);
    }

    /**
     * @param  Model | class-string<Model> | Closure | null  $model
     */
    public function actionSchemaModel(Model | string | Closure | null $model): static
    {
        $this->actionSchemaModel = $model;

        return $this;
    }

    /**
     * @return Model | array<string, mixed> | class-string<Model> | null
     */
    public function getActionSchemaModel(): Model | array | string | null
    {
        return $this->evaluate($this->actionSchemaModel) ?? $this->getRecord() ?? $this->getModel();
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->getActions());
    }
}
