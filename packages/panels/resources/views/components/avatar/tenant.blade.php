@props([
    'tenant' => filament()->getTenant(),
])

<x-filament::avatar
    :src="filament()->getTenantAvatarUrl($tenant)"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-avatar rounded-md'])
    "
/>
