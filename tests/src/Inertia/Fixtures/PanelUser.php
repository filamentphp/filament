<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Panel;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PanelUser extends User
{
    protected $table = 'users';

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($panel->getId(), ['inertia-tests', 'inertia-tenancy-tests']) || parent::canAccessPanel($panel);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user', 'user_id');
    }
}
