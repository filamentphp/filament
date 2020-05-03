<?php

namespace Filament\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('id', $query)
                ->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('description', 'like', '%'.$query.'%');
    }
}