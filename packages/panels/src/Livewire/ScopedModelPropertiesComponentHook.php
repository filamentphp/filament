<?php

namespace Filament\Livewire;

use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Resources\Pages\Page as ResourcePage;
use Livewire\ComponentHook;

class ScopedModelPropertiesComponentHook extends ComponentHook
{
    public function skip(): bool
    {
        return ! (($this->component instanceof ResourcePage) || ($this->component instanceof EditTenantProfile));
    }

    public function hydrate(): void
    {
        $this->component->resolveScopedModelProperties();
    }

    public function call(string $method): void
    {
        abort_if($method === 'resolveScopedModelProperties', 404);

        $this->component->resolveScopedModelProperties();
    }
}
