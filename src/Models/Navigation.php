<?php

namespace Filament\Models;

use Illuminate\Database\Eloquent\Model;

use Filament\Casts\Json;

class Navigation extends Model
{
    use \Sushi\Sushi;

    protected $guarded = [];

    public function getRows()
    {
        return config('filament.nav', []);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => Json::class,
    ];
}