<?php

namespace Filament\Forms\Components;

class Grid extends Component
{
    protected $columns = 2;

    public function columns($columns)
    {
        $this->configure(function () use ($columns) {
            $this->columns = $columns;
        });

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getSubform()
    {
        return parent::getSubform()->columns($this->columns);
    }

    public static function make($schema = [])
    {
        return (new static())->schema($schema);
    }
}
