<?php

namespace Filament\Models\Contracts;

use Filament\Context;
use Illuminate\Database\Eloquent\Model;

interface HasDefaultTenant
{
    public function getDefaultTenant(Context $context): ?Model;
}
