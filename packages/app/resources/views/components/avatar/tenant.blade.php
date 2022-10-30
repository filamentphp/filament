@props([
    'tenant' => filament()->getTenant(),
])

<x-filament::avatar
    :src="filament()->getTenantAvatarUrl($tenant)"
    {{ $attributes }}
/>
