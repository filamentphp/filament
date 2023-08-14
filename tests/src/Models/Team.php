<?php

namespace Filament\Tests\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Filament\Tests\Database\Factories\TeamFactory;
use Filament\Tests\Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return TeamFactory::new();
    }
}
