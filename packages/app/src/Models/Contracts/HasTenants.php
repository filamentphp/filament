<?php

namespace Filament\Models\Contracts;

use Filament\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface HasTenants
{
    public function canAccessTenant(Model $tenant): bool;

    /**
     * @return array<Model> | Collection
     */
    public function getTenants(Context $context): array | Collection;
}
