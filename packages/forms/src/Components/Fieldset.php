<?php

namespace Filament\Forms\Components;

class Fieldset extends Component
{
    public $columns = 2;

    public static function make($label, $schema = [])
    {
        return (new static())
            ->label($label)
            ->schema($schema);
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getSubform()
    {
        return parent::getSubform()->columns($this->columns);
    }
}
