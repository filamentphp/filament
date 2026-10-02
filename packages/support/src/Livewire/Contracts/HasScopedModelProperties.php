<?php

namespace Filament\Support\Livewire\Contracts;

interface HasScopedModelProperties
{
    /** @param array<string, mixed> | null $properties */
    public function resolveScopedModelProperties(?array $properties = null): void;
}
