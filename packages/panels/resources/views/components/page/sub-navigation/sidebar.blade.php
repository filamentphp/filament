@props([
    'navigation',
])

@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
@endphp

<div
    {{ $attributes->class(['fi-page-sub-navigation-sidebar-ctn']) }}
>
    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <ul class="fi-page-sub-navigation-sidebar">
        @foreach ($navigation as $navigationGroup)
            @php
                $isNavigationGroupActive = $navigationGroup->isActive();
                $isNavigationGroupCollapsible = $navigationGroup->isCollapsible();
                $navigationGroupIcon = $navigationGroup->getIcon();
                $navigationGroupItems = $navigationGroup->getItems();
                $navigationGroupLabel = $navigationGroup->getLabel();
                $navigationGroupExtraSidebarAttributeBag = $navigationGroup->getExtraSidebarAttributeBag();
            @endphp

            <x-filament-panels::sidebar.group
                :active="$isNavigationGroupActive"
                :collapsible="$isNavigationGroupCollapsible"
                :icon="$navigationGroupIcon"
                :items="$navigationGroupItems"
                :label="$navigationGroupLabel"
                :sidebar-collapsible="false"
                sub-navigation
                :attributes="\Filament\Support\prepare_inherited_attributes($navigationGroupExtraSidebarAttributeBag)"
            />
        @endforeach
    </ul>

    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_SIDEBAR_AFTER, scopes: $this->getRenderHookScopes()) }}
</div>
