<?php

namespace Filament\Tests\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserWithHiddenTeamPivot extends User
{
    protected $table = 'users';

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
            ->using(HiddenTeamUser::class)
            ->withPivot(['id', 'role'])
            ->withTimestamps()
            ->as('membership');
    }
}
