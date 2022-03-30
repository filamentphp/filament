<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Actions\Action;

trait HasActions
{
    protected array $actions = [];

    public function registerActions(array $actions): static
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function getAction(string $name): ?Action
    {
        $action = $this->actions[$name] ?? null;

        if ($action === null) {
            return null;
        }

        return $action->component($this);
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->actions);
    }
}
