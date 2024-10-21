@props([
    'navigation',
])

<div {{ $attributes->class(['fi-page-sub-navigation-sidebar hidden w-72 flex-col gap-y-7 md:flex']) }}>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_START, scopes: $this->getRenderHookScopes()) }}

    <ul
        wire:ignore
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

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_END, scopes: $this->getRenderHookScopes()) }}

</div>
