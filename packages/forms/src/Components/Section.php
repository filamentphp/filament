<?php

namespace Filament\Forms\Components;

class Section extends Component
{
    protected $columns = 1;

    protected $heading;

    protected $subheading;

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

    public function getHeading()
    {
        return $this->heading;
    }

    public function getSubform()
    {
        return parent::getSubform()->columns($this->columns);
    }

    public function getSubheading()
    {
        return $this->subheading;
    }

    public function heading($heading)
    {
        $this->configure(function () use ($heading) {
            $this->heading = $heading;
        });

        return $this;
    }

    public static function make($heading, $subheading = null, $schema = [])
    {
        return (new static())
            ->heading($heading)
            ->subheading($subheading)
            ->schema($schema);
    }

    public function subheading($subheading)
    {
        $this->configure(function () use ($subheading) {
            $this->subheading = $subheading;
        });

        return $this;
    }
}
