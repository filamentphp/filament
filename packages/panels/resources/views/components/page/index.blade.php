@props([
    'fullHeight' => false,
])

@php
    $subNavigation = $this->getCachedSubNavigation();
    $widgetData = $this->getWidgetData();
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
                'flex flex-col gap-8 md:flex-row' => $subNavigation,
                'h-full' => $fullHeight,
            ])
        >
            @if ($subNavigation)
                <div wire:ignore>
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

                    <div class="hidden w-72 md:block">
                        @foreach ($subNavigation as $subNavigationGroup)
                            <x-filament-panels::sidebar.group
                                :collapsible="$subNavigationGroup->isCollapsible()"
                                :icon="$subNavigationGroup->getIcon()"
                                :items="$subNavigationGroup->getItems()"
                                :label="$subNavigationGroup->getLabel()"
                                :sidebar-collapsible="false"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            <div
                @class([
                    'grid flex-1 auto-cols-fr gap-y-8',
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

    @if (! ($this instanceof \Filament\Tables\Contracts\HasTable))
        <x-filament-actions::modals />
    @elseif ($this->isTableLoaded() &&
             filled($this->defaultTableAction) &&
             filled($this->defaultTableActionRecord))
        <div
            wire:init="mountTableAction(@js($this->defaultTableAction), @js($this->defaultTableActionRecord))"
        ></div>
    @endif

    @if (filled($this->defaultAction))
        <div
            wire:init="mountAction(@js($this->defaultAction) @if (filled($this->defaultActionArguments)) , @js($this->defaultActionArguments) @endif)"
        ></div>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::page.end', scopes: $this->getRenderHookScopes()) }}
</div>
