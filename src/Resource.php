<?php

namespace Filament;

use Illuminate\Support\Str;
use Filament\Contracts\Resource as ResourceContract;

abstract class Resource implements ResourceContract
{ 
    public $group = null;
    public $icon = 'heroicon-o-database';
    public $sort = 0;

    /** @return string */
    public function label()
    {
        return $this->label ?? (string) Str::of(class_basename(get_called_class()))->kebab()->replace('-', ' ')->plural()->title();
    }

    /** @return array */
    public function actions()
    {
        return [];
    }
}