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
        public readonly Model $tenant,
        public readonly Model | Authenticatable | HasTenants $user,
    ) {
    }
}
