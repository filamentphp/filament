@props([
    'tenant' => filament()->getTenant(),
])

@php
    use Illuminate\Contracts\Support\Htmlable;

    $src = filament()->getTenantAvatarUrl($tenant);
    $name = filament()->getTenantName($tenant);
    $alt = __('filament-panels::layout.avatar.alt', ['name' => ($name instanceof Htmlable) ? html_entity_decode(strip_tags($name->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8') : $name]);
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
