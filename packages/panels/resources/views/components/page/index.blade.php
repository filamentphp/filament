@props([
    'fullHeight' => false,
])

@php
    use Filament\Pages\Enums\SubNavigationPosition;

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
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="fi-page-header-main-ctn">
        @if ($subNavigation)
            <div
                class="fi-page-main-sub-navigation-mobile-menu-render-hook-ctn"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_BEFORE, scopes: $this->getRenderHookScopes()) }}
            </div>

            <x-filament-panels::page.sub-navigation.mobile-menu
                :navigation="$subNavigation"
            />

            <div
                class="fi-page-main-sub-navigation-mobile-menu-render-hook-ctn"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_MOBILE_MENU_AFTER, scopes: $this->getRenderHookScopes()) }}
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
                    @if ($heading instanceof \Illuminate\Contracts\Support\Htmlable)
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>
                    @endif

                    @if ($subheading instanceof \Illuminate\Contracts\Support\Htmlable)
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
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_START_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    <x-filament-panels::page.sub-navigation.sidebar
                        :navigation="$subNavigation"
                    />

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_START_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif

                @if ($subNavigationPosition === SubNavigationPosition::Top)
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_TOP_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    <x-filament-panels::page.sub-navigation.tabs
                        :navigation="$subNavigation"
                    />

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_TOP_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif
            @endif

            <div class="fi-page-content">
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->headerWidgets }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_FOOTER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->footerWidgets }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>

            @if ($subNavigation && $subNavigationPosition === SubNavigationPosition::End)
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_END_BEFORE, scopes: $this->getRenderHookScopes()) }}

                <x-filament-panels::page.sub-navigation.sidebar
                    :navigation="$subNavigation"
                />

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_END_AFTER, scopes: $this->getRenderHookScopes()) }}
            @endif
        </div>

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </div>

    @if (! ($this instanceof \Filament\Tables\Contracts\HasTable))
        <x-filament-actions::modals />
    @elseif ($this->isTableLoaded() && filled($this->defaultTableAction))
        <div
            wire:init="mountAction(@js($this->defaultTableAction) , @if (filled($this->defaultTableActionArguments)) @js($this->defaultTableActionArguments) @else {} @endif , @js(['table' => true, 'recordKey' => $this->defaultTableActionRecord]))"
        ></div>
    @endif

    @if (filled($this->defaultAction))
        <div
            wire:init="mountAction(@js($this->defaultAction) @if (filled($this->defaultActionArguments) || filled($this->defaultActionContext)) , @if (filled($this->defaultActionArguments)) @js($this->defaultActionArguments) @else {} @endif @endif @if (filled($this->defaultActionContext)) , @js($this->defaultActionContext) @endif)"
        ></div>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_END, scopes: $this->getRenderHookScopes()) }}

    @if (method_exists($this, 'hasUnsavedDataChangesAlert') && $this->hasUnsavedDataChangesAlert())
        @if (\Filament\Support\Facades\FilamentView::hasSpaMode())
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

    @if ((! app()->hasDebugModeEnabled()) && $this->hasErrorNotifications())
        @script
            <script>
                const errorNotifications = @js($this->getErrorNotifications())

                Livewire.hook('request', ({ payload, fail }) => {
                    fail(({ status, preventDefault }) => {
                        if (JSON.parse(payload).components.length === 1) {
                            for (const component of JSON.parse(payload)
                                .components) {
                                if (
                                    JSON.parse(component.snapshot).data
                                        .isFilamentNotificationsComponent
                                ) {
                                    return
                                }
                            }
                        }

                        preventDefault()

                        const errorNotification =
                            errorNotifications[status] ?? errorNotifications['']

                        new FilamentNotification()
                            .title(errorNotification.title)
                            .body(errorNotification.body)
                            .danger()
                            .send()
                    })
                })
            </script>
        @endscript
    @endif

    <x-filament-panels::unsaved-action-changes-alert />
</div>
