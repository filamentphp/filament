<?php

namespace Filament\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

interface HasTenants
{
    public function canAccessTenant(Model $tenant): bool;

    public function getTenants(): array | Arrayable;
}
