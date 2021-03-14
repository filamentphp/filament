<?php

namespace Filament\Forms\Components;

class Section extends Component
{
    protected $columns = 1;

    protected $heading;

    protected $subheading;

    protected $collapsible;

    protected $collapsed = false;

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

    public function getSubheading()
    {
        return $this->subheading;
    }

    public function getCollapsible()
    {
        return $this->collapsible;
    }

    public function getCollapsed()
    {
        return $this->collapsed;
    }

    public function getSubform()
    {
        return parent::getSubform()->columns($this->columns);
    }

    public function heading($heading)
    {
        $this->configure(function () use ($heading) {
            $this->heading = $heading;
        });

        return $this;
    }

    public static function make($heading, $subheading = null, $schema = [], $collapsible = false)
    {
        return (new static())
            ->heading($heading)
            ->subheading($subheading)
            ->schema($schema)
            ->collapsible($collapsible);
    }

    public function subheading($subheading)
    {
        $this->configure(function () use ($subheading) {
            $this->subheading = $subheading;
        });

        return $this;
    }

    public function collapsed()
    {
        $this->collapsed = true;
        $this->collapsible = true;

        return $this;
    }

    public function collapsible($collapsible)
    {
        $this->collapsible = $collapsible;

        return $this;
    }
}
