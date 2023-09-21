@php
    $widgetData = $this->getWidgetData();
    $subNavigation = $this->getCachedSubNavigation();
@endphp

<div {{ $attributes->class(['fi-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start', scopes: $this->getRenderHookScopes()) }}

    <section class="grid auto-cols-fr gap-y-8 py-8">
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            <x-filament-panels::header
                :actions="$this->getCachedHeaderActions()"
                :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []"
                :heading="$heading"
                :subheading="$this->getSubheading()"
            />
        @endif

        <div
            @class([
                'grid grid-cols-1 gap-6 md:grid-cols-4' => $subNavigation,
            ])
        >
            @if ($subNavigation)
                <div class="col-span-1">
                    <div
                        class="rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                    >
                        @foreach ($subNavigation as $subNavigationGroup)
                            <x-filament-panels::sidebar.group
                                :collapsible="$subNavigationGroup->isCollapsible()"
                                :icon="$subNavigationGroup->getIcon()"
                                :items="$subNavigationGroup->getItems()"
                                :label="$subNavigationGroup->getLabel()"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            <div
                @class([
                    'grid auto-cols-fr gap-y-8',
                    'col-span-1 md:col-span-3' => $subNavigation,
                ])
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.header-widgets.before', scopes: $this->getRenderHookScopes()) }}

                @if ($headerWidgets = $this->getVisibleHeaderWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getHeaderWidgetsColumns()"
                        :data="$widgetData"
                        :widgets="$headerWidgets"
                    />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.header-widgets.after', scopes: $this->getRenderHookScopes()) }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.footer-widgets.before', scopes: $this->getRenderHookScopes()) }}

                @if ($footerWidgets = $this->getVisibleFooterWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getFooterWidgetsColumns()"
                        :data="$widgetData"
                        :widgets="$footerWidgets"
                    />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.footer-widgets.after', scopes: $this->getRenderHookScopes()) }}
            </div>
        </div>

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </section>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.end', scopes: $this->getRenderHookScopes()) }}
</div>
