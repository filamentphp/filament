<?php

namespace Filament\Resources\Forms\Components;

use Filament\Forms\Components\Component;

class RelationManager extends Component
{
    use Concerns\InteractsWithResource;

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
