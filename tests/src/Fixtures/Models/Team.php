<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Tests\Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model implements HasAvatar
{
    use HasFactory;

    protected $guarded = [];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getAttribute('avatar_url');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class, 'team_id', 'author_id');
    }

    protected static function newFactory()
    {
        return TeamFactory::new();
    }
}
