<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasActions
{
    /**
     * @var array<string, Action>
     */
    protected array $actions = [];

    protected Model | string | null $actionFormModel = null;

    /**
     * @param  array<string, Action>  $actions
     */
    public function registerActions(array $actions): static
    {
        foreach ($actions as $action) {
            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Form component actions must be an instance of ' . Action::class . '.');
            }

            $this->actions[$action->getName()] = $action->component($this);
        }

        return $this;
    }

    public function getAction(string $name): ?Action
    {
        return ($this->getActions()[$name] ?? null)?->component($this);
    }

    /**
     * @return array<string, Action>
     */
    public function getActions(): array
    {
        return $this->actions;
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
