<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Section extends Component
{
    use Concerns\CanConcealFields;

    protected $collapsed = false;

    protected $collapsible = false;

    protected $columns = 1;

    protected $heading;

    protected $subheading;

    public function collapsed()
    {
        $this->configure(function () {
            $this->collapsed = true;
            $this->collapsible = true;
        });

        return $this;
    }

    public function collapsible($collapsible)
    {
        $this->configure(function () use ($collapsible) {
            $this->collapsible = $collapsible;
        });

        return $this;
    }

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

    public function isCollapsed()
    {
        return $this->collapsed;
    }

    public function isCollapsible()
    {
        return $this->collapsible;
    }

    public static function make($heading, $subheading = null, $schema = [])
    {
        return (new static())
            ->heading($heading)
            ->id(Str::slug($heading))
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
