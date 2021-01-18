<?php

namespace Filament;

use Filament\Contracts\Resource as ResourceContract;
use Illuminate\Support\Str;

abstract class Resource implements ResourceContract
{
    public $enabled = true;

    public $icon = 'heroicon-o-database';

    public $sort = 0;

    public function actions()
    {
        return [];
    }

    public function label()
    {
        return $this->label ?? (string) Str::of(class_basename(get_called_class()))->kebab()->replace('-', ' ')->plural()->title();
    }
}
