@props([
    'tenant' => filament()->getTenant(),
])

<x-filament::avatar
    :circular="false"
    :src="filament()->getTenantAvatarUrl($tenant)"
    :alt="__('filament-panels::layout.avatar.alt', ['name' => filament()->getTenantName($tenant)])"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-avatar'])
    "
/>
