<?php

namespace Filament\Models\Contracts;

interface HasCurrentTenantLabel
{
    public function getCurrentTenantLabel(): string;
}
