<?php

namespace Filament\Tests\Models;

use Filament\Tests\Database\Factories\DomainTeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainTeam extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return DomainTeamFactory::new();
    }
}
