<?php

namespace Filament\Models;

use Filament\Traits\FillsColumns;

class Permission extends \Spatie\Permission\Models\Permission
{
    use FillsColumns;
    
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('id', $query)
                ->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('description', 'like', '%'.$query.'%');
    }
}