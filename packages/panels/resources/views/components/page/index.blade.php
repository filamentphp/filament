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
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            @php
                $headerActions = $this->getCachedHeaderActions();
                $breadcrumbs = filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : [];
                $subheading = $this->getSubheading();
            @endphp

            <x-filament-panels::header
                :actions="$headerActions"
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

        <div class="fi-page-main">
            @if ($subNavigation)
                <div class="fi-page-main-sub-navigation-select-render-hook-ctn">
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SELECT_BEFORE, scopes: $this->getRenderHookScopes()) }}
                </div>

                <x-filament-panels::page.sub-navigation.select
                    :navigation="$subNavigation"
                />

                <div class="fi-page-main-sub-navigation-select-render-hook-ctn">
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_SUB_NAVIGATION_SELECT_AFTER, scopes: $this->getRenderHookScopes()) }}
                </div>

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
                {{ $this->headerWidgets }}

                {{ $slot }}

                {{ $this->footerWidgets }}
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
    @endif

    @if (filled($this->defaultAction))
        <div
            wire:init="mountAction(@js($this->defaultAction) @if (filled($this->defaultActionArguments) || filled($this->defaultActionContext)) , @if (filled($this->defaultActionArguments)) @js($this->defaultActionArguments) @else {} @endif @endif @if (filled($this->defaultActionContext)) @js($this->defaultActionContext) @endif)"
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

    <x-filament-panels::unsaved-action-changes-alert />
</div>
