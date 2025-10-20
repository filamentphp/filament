@props([
    'tenant' => filament()->getTenant(),
])

@php
    $src = filament()->getTenantAvatarUrl($tenant);
    $alt = __('filament-panels::layout.avatar.alt', ['name' => filament()->getTenantName($tenant)]);
@endphp

<x-filament::avatar
    :circular="false"
    :src="$src"
    :alt="$alt"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-tenant-avatar'])
    "
/>
