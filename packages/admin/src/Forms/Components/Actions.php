<?php

namespace Filament\Forms\Components;

class Actions extends Component
{
    protected string $view = 'filament::forms.components.actions';

    public $actions = [];

    final public function __construct(array | callable $actions = [])
    {
        $this->actions($actions);
    }

    public static function make(array | callable $actions = []): static
    {
        $static = new static($actions);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');
    }

    public function actions(array | callable $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->evaluate($this->actions);
    }
}
