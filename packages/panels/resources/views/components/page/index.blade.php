@props([
    'fullHeight' => false,
])

@php
    $widgetData = $this->getWidgetData();
    $subNavigation = $this->getCachedSubNavigation();
@endphp

<div
    {{
        $attributes->class([
            'fi-page',
            'h-full' => $fullHeight,
        ])
    }}
>
    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.start', scopes: $this->getRenderHookScopes()) }}

    <section
        @class([
            'flex flex-col gap-y-8 py-8',
            'h-full' => $fullHeight,
        ])
    >
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
                'h-full' => $fullHeight,
            ])
        >
            @if ($subNavigation)
                <div wire:ignore class="col-span-1">
                    <x-filament::input.wrapper class="md:hidden">
                        <x-filament::input.select
                            x-data="{}"
                            x-on:change="window.location = $event.target.value"
                        >
                            @foreach ($subNavigation as $subNavigationGroup)
                                @php
                                    $subNavigationGroupLabel = $subNavigationGroup->getLabel();
                                @endphp

                                @if (filled($subNavigationGroupLabel))
                                    <optgroup
                                        label="{{ $subNavigationGroupLabel }}"
                                    >
                                        @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                                            <option
                                                @if ($subNavigationItem->isActive())
                                                    selected
                                                @endif
                                                value="{{ $subNavigationItem->getUrl() }}"
                                            >
                                                {{ $subNavigationItem->getLabel() }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                                        <option
                                            @if ($subNavigationItem->isActive())
                                                selected
                                            @endif
                                            value="{{ $subNavigationItem->getUrl() }}"
                                        >
                                            {{ $subNavigationItem->getLabel() }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>

                    <div
                        class="hidden rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:block"
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
                    'h-full' => $fullHeight,
                ])
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.header-widgets.before', scopes: $this->getRenderHookScopes()) }}

                @if ($headerWidgets = $this->getVisibleHeaderWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getHeaderWidgetsColumns()"
                        :data="$widgetData"
                        :widgets="$headerWidgets"
                        class="fi-page-header-widgets"
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
                        class="fi-page-footer-widgets"
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
