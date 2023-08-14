<?php

namespace Filament\Tests\Models;

use Filament\Tests\Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return TeamFactory::new();
    }
}
