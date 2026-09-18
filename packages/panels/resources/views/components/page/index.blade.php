@props([
    'fullHeight' => false,
])

@php
    use Filament\Pages\Enums\SubNavigationPosition;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Contracts\HasTable;
    use Filament\View\PanelsRenderHook;
    use Illuminate\Contracts\Support\Htmlable;

    $subNavigation = $this->getCachedSubNavigation();
    $subNavigationPosition = $this->getSubNavigationPosition();
    $widgetData = $this->getWidgetData();
@endphp

<div
    {{
        $attributes->class([
            'fi-page',
            'fi-height-full' => $fullHeight,
            'fi-page-has-sub-navigation' => $subNavigation,
            "fi-page-has-sub-navigation-{$subNavigationPosition->value}" => $subNavigation,
            ...$this->getPageClasses(),
        ])
    }}
>
    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="fi-page-header-main-ctn">
        @if ($subNavigation)
            <div
                class="fi-page-main-sub-navigation-mobile-menu-render-hook-ctn"
            >
                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_BEFORE, scopes: $this->getRenderHookScopes()) }}
            </div>

            <x-filament-panels::page.sub-navigation.mobile-menu
                :navigation="$subNavigation"
            />

            <div
                class="fi-page-main-sub-navigation-mobile-menu-render-hook-ctn"
            >
                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>
        @endif

        @if ($header = $this->getHeader())
            {{ $header }}
        @else
            @php
                $heading = $this->getHeading();
                $headerActions = $this->getCachedHeaderActions();
                $headerActionsAlignment = $this->getHeaderActionsAlignment();
                $breadcrumbs = filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : [];
                $subheading = $this->getSubheading();
            @endphp

            @if (filled($headerActions) || $breadcrumbs || filled($heading) || filled($subheading))
                <x-filament-panels::header
                    :actions="$headerActions"
                    :actions-alignment="$headerActionsAlignment"
                    :breadcrumbs="$breadcrumbs"
                    :heading="$heading"
                    :subheading="$subheading"
                >
                    @if ($heading instanceof Htmlable)
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>
                    @endif

                    @if ($subheading instanceof Htmlable)
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                </x-filament-panels::header>
            @endif
        @endif

        <div class="fi-page-main">
            @if ($subNavigation)
                @if ($subNavigationPosition === SubNavigationPosition::Start)
                    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_START_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    <x-filament-panels::page.sub-navigation.sidebar
                        :navigation="$subNavigation"
                    />

                    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_START_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif

                @if ($subNavigationPosition === SubNavigationPosition::Top)
                    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_TOP_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    <x-filament-panels::page.sub-navigation.tabs
                        :navigation="$subNavigation"
                    />

                    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_TOP_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif
            @endif

            <div class="fi-page-content">
                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->headerWidgets }}

                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_HEADER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}

                {{ $slot }}

                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_FOOTER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->footerWidgets }}

                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>

            @if ($subNavigation && $subNavigationPosition === SubNavigationPosition::End)
                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_END_BEFORE, scopes: $this->getRenderHookScopes()) }}

                <x-filament-panels::page.sub-navigation.sidebar
                    :navigation="$subNavigation"
                />

                {{ FilamentView::renderHook(PanelsRenderHook::PAGE_SUB_NAVIGATION_END_AFTER, scopes: $this->getRenderHookScopes()) }}
            @endif
        </div>

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    @if (! ($this instanceof HasTable))
        <x-filament-actions::modals />
    @elseif ($this->isTableLoaded() && filled($this->defaultTableAction))
        <div
            wire:init="mountAction(@js($this->defaultTableAction) , @if (filled($this->defaultTableActionArguments)) @js($this->defaultTableActionArguments) @else {} @endif , @js($this->getDefaultTableActionUrlContext()))"
        ></div>
    @endif

    @if (filled($this->defaultAction))
        <div
            wire:init="mountAction(@js($this->defaultAction) , @if (filled($this->defaultActionArguments)) @js($this->defaultActionArguments) @else {} @endif , @js($this->getDefaultActionUrlContext()))"
        ></div>
    @endif

    {{ FilamentView::renderHook(PanelsRenderHook::PAGE_END, scopes: $this->getRenderHookScopes()) }}

    @if (method_exists($this, 'hasUnsavedDataChangesAlert') && $this->hasUnsavedDataChangesAlert())
        @if (FilamentView::hasSpaMode())
            @script
                <script>
                    setUpSpaModeUnsavedDataChangesAlert({
                        body: @js(__('filament-panels::unsaved-changes-alert.body')),
                        resolveLivewireComponentUsing: () => @this,
                        $wire,
                    })
                </script>
            @endscript
        @else
            @script
                <script>
                    setUpUnsavedDataChangesAlert({ $wire })
                </script>
            @endscript
        @endif
    @endif

    @if (! app()->hasDebugModeEnabled())
        @script
            <script>
                window.filamentErrorNotifications = @js($this->hasErrorNotifications() ? $this->getErrorNotifications() : null)
            </script>
        @endscript
    @endif

    <x-filament-panels::unsaved-action-changes-alert />
</div>
