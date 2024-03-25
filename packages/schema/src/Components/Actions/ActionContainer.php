<?php

namespace Filament\Schema\Components\Actions;

use Filament\Actions\Action;
use Filament\Schema\Components\Component;

class ActionContainer extends Component
{
    protected string $view = 'filament-schema::components.actions.action-container';

    protected Action $action;

    final public function __construct(Action $action)
    {
        $this->action = $action;
        $this->registerActions([$action]);
    }

    public static function make(Action $action): static
    {
        $static = app(static::class, ['action' => $action]);
        $static->configure();

        return $static;
    }

    public function getKey(): string
    {
        return parent::getKey() ?? "{$this->getStatePath()}.{$this->action->getName()}Action";
    }

    public function isHidden(): bool
    {
        return $this->action->isHidden();
    }
}
