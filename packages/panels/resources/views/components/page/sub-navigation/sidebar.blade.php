@props([
    'navigation',
])

<ul
    wire:ignore
    {{ $attributes->class(['hidden w-72 flex-col gap-y-7 md:flex']) }}
>
    @foreach ($navigation as $navigationGroup)
        <x-filament-panels::sidebar.group
            :collapsible="$navigationGroup->isCollapsible()"
            :icon="$navigationGroup->getIcon()"
            :items="$navigationGroup->getItems()"
            :label="$navigationGroup->getLabel()"
            :sidebar-collapsible="false"
        />
    @endforeach
</ul>
