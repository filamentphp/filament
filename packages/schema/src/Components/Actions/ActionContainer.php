<?php

namespace Filament\Schema\Components\Actions;

use Filament\Actions\Action;
use Filament\Schema\Components\Component;

class ActionContainer extends Component
{
    protected string $view = 'filament-schema::components.actions.action-container';

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(fn (): string => "{$this->getAction()->getName()}Action");
    }

    public function isHidden(): bool
    {
        return $this->action->isHidden();
    }
}
