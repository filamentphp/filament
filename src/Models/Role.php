<?php

namespace Filament\Models;

class Role extends \Spatie\Permission\Models\Role
{
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('name', 'like', '%'.$query.'%')
                ->orWhere('description', 'like', '%'.$query.'%');
    }
}