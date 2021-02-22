<?php

namespace Filament\Forms\Components;

class RelationManager extends Component
{
    public $manager;

    public static function make($manager)
    {
        return (new static())->manager($manager);
    }

    public function manager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    public function render()
    {
        if (! $this->record) return;

        return parent::render();
    }
}
