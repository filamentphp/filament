<?php

namespace Filament;

use Illuminate\Support\Str;
use Filament\Contracts\Resource;

abstract class FilamentResource implements Resource
{ 
    public $group = null;
    public $icon = 'heroicon-o-document-text';
    public $sort = 0;

    /** @return string */
    public function label()
    {
        return $this->label ?? (string) Str::of(class_basename(get_class($this)))->kebab()->replace('-', ' ')->plural()->title();
    }

    /** @return array */
    public function actions()
    {
        return [];
    }
}