<?php

namespace Filament\Events;

use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class TenantSet
{
    use Dispatchable;

    public function __construct(
        protected Model $tenant,
        protected Model | Authenticatable | HasTenants $user,
    ) {
    }

    /**
     * @return Model
     */
    public function getTenant(): Model
    {
        return $this->tenant;
    }

    /**
     * @return HasTenants | Authenticatable | Model
     */
    public function getUser(): Model | HasTenants | Authenticatable
    {
        return $this->user;
    }
}
