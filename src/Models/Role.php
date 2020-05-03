<?php

namespace Filament\Models;

use Plank\Metable\Metable;

class Role extends \Spatie\Permission\Models\Role
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
}