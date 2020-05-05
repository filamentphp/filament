<?php

namespace Filament\Models;

use Nicolaslopezj\Searchable\SearchableTrait;
use Plank\Metable\Metable;

class Permission extends \Spatie\Permission\Models\Permission
{
    use SearchableTrait, Metable;

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
    
    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'permissions.name' => 10,
            'permissions.description' => 10,
            'permissions.id' => 1,
        ],
    ];
}