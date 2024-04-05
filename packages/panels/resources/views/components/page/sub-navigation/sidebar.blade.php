@props([
    'navigation',
])

<ul
    wire:ignore
    {{ $attributes->class(['fi-page-sub-navigation-sidebar hidden w-72 flex-col gap-y-7 md:flex']) }}
>
    @foreach ($navigation as $navigationGroup)
        <x-filament-panels::sidebar.group
            :active="$navigationGroup->isActive()"
            :collapsible="$navigationGroup->isCollapsible()"
            :icon="$navigationGroup->getIcon()"
            :items="$navigationGroup->getItems()"
            :label="$navigationGroup->getLabel()"
            :sidebar-collapsible="false"
            sub-navigation
            :attributes="\Filament\Support\prepare_inherited_attributes($navigationGroup->getExtraSidebarAttributeBag())"
        />
    @endforeach
</ul>
