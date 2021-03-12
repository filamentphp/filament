<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Tab extends Component
{
    protected $columns = 1;

    public static function make($label, $schema = [])
    {
        return (new static())
            ->label($label)
            ->id(Str::slug($label))
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
