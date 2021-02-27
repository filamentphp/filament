<?php

namespace Filament\Forms\Components;

class Section extends Component
{
    public $columns = 1;

    public $heading;

    public $subheading;

    public static function make($heading, $subheading = null, $schema = [])
    {
        return (new static())
            ->heading($heading)
            ->subheading($subheading)
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

    public function heading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    public function subheading($subheading)
    {
        $this->subheading = $subheading;

        return $this;
    }
}
