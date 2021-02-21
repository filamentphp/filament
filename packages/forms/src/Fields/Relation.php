<?php

namespace Filament\Forms\Fields;

class Relation extends Field
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
