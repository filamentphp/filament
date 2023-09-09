<?php

namespace Filament\Infolists\Components\Actions;

use Filament\Infolists\Components\Component;

class ActionContainer extends Component
{
    protected string $view = 'filament-infolists::components.actions.action-container';

    final public function __construct(Action $action)
    {
        $this->action($action);
    }

    public static function make(Action $action): static
    {
        $static = app(static::class, ['action' => $action]);
        $static->configure();

        return $static;
    }

    public function getKey(): string
    {
        return parent::getKey() ?? "{$this->getAction()->getName()}Action";
    }

    public function isHidden(): bool
    {
        return $this->getAction()->isHidden();
    }
}
