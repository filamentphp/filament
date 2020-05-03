<?php

namespace Filament\Models;

use Plank\Metable\Metable;

class Permission extends \Spatie\Permission\Models\Permission
{
    use Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description',
        'guard_name',
    ];
    
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('id', $query)
                ->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('description', 'like', '%'.$query.'%');
    }
}