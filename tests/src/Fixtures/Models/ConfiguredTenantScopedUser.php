<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ConfiguredTenantScopedUser extends User
{
    protected $table = 'users';

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id');
    }
}
