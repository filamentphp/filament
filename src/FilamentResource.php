<?php

namespace Filament;

use Filament\Contracts\Resource;

abstract class FilamentResource implements Resource
{ 
    public $icon = 'heroicon-o-document-text';
    public $sort = 0;
    public $hideFromNav = false;

    public function actions(): array
    {
        return [];
    }
}