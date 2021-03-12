<?php

namespace Filament\Forms\Components;

class Grid extends Component
{
    protected $columns = 2;

    public static function make($schema = [])
    {
        return (new static())->schema($schema);
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
