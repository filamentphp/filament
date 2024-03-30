@props([
    'tenant' => filament()->getTenant(),
])

<x-filament::avatar
    :circular="false"
    :alt="__('filament-panels::layout.avatar.alt', ['name' => filament()->getTenantName($tenant)])"
    :src="filament()->getTenantAvatarUrl($tenant)"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-avatar'])
    "
/>
