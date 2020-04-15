<?php

namespace Filament\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Traits\FillsColumns;

class GlobalSet extends Model
{
    use FillsColumns;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fields' => 'array',
    ];
    
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('id', $query)
                ->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('fields', 'like', '%'.$query.'%');
    }
}