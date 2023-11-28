@props([
    'fullHeight' => false,
])

@php
    use Filament\Pages\SubNavigationPosition;

    $subNavigation = $this->getCachedSubNavigation();
    $subNavigationPosition = $this->getSubNavigationPosition();
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
                'flex flex-col gap-8' => $subNavigation,
                match ($subNavigationPosition) {
                    SubNavigationPosition::Start, SubNavigationPosition::End => 'md:flex-row',
                    default => null,
                } => $subNavigation,
                'h-full' => $fullHeight,
            ])
        >
            @if ($subNavigation)
                <x-filament::input.wrapper class="md:hidden" wire:ignore>
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
                            @endif
                                    @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                                        @foreach ([$subNavigationItem, ...$subNavigationItem->getChildItems()] as $subNavigationItemChild)
                                            <option
                                                @if ($subNavigationItem->isActive())
                                                    selected
                                                @endif
                                                value="{{ $subNavigationItem->getUrl() }}"
                                            >
                                                @if ($loop->index)&ensp;&ensp;@endif{{ $subNavigationItem->getLabel() }}
                                            </option>
                                        @endforeach
                                    @endforeach
                            @if (filled($subNavigationGroupLabel))
                                </optgroup>
                            @endif
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                @if ($subNavigationPosition === SubNavigationPosition::Start)
                    <x-filament-panels::page.sub-navigation
                        :navigation="$subNavigation"
                    />
                @endif

                @if ($subNavigationPosition === SubNavigationPosition::Top)
                    <x-filament::tabs>
                        @foreach ($subNavigation as $subNavigationGroup)
                            @if ($subNavigationGroupLabel = $subNavigationGroup->getLabel())
                                <x-filament::dropdown placement="bottom-start">
                                    <x-slot name="trigger">
                                        <x-filament::tabs.item
                                            :active="$subNavigationGroup->isActive()"
                                            :icon="$subNavigationGroup->getIcon()"
                                        >
                                            {{ $subNavigationGroupLabel }}
                                        </x-filament::tabs.item>
                                    </x-slot>

                                    <x-filament::dropdown.list>
                                        @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                                            @php
                                                $icon = $subNavigationItem->getIcon();
                                            @endphp

                                            <x-filament::dropdown.list.item
                                                :badge="$subNavigationItem->getBadge()"
                                                :badge-color="$subNavigationItem->getBadgeColor()"
                                                :href="$subNavigationItem->getUrl()"
                                                :icon="$subNavigationItem->isActive() ? ($subNavigationItem->getActiveIcon() ?? $icon) : $icon"
                                                tag="a"
                                                :target="$subNavigationItem->shouldOpenUrlInNewTab() ? '_blank' : null"
                                            >
                                                {{ $subNavigationItem->getLabel() }}
                                            </x-filament::dropdown.list.item>
                                        @endforeach
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            @else
                                @foreach ($subNavigationGroup->getItems() as $subNavigationItem)
                                    @php
                                        $icon = $subNavigationItem->getIcon();
                                    @endphp

                                    <x-filament::tabs.item
                                        :active="$subNavigationItem->isActive()"
                                        :badge="$subNavigationItem->getBadge()"
                                        :badge-color="$subNavigationItem->getBadgeColor()"
                                        :href="$subNavigationItem->getUrl()"
                                        :icon="$subNavigationItem->isActive() ? ($subNavigationItem->getActiveIcon() ?? $icon) : $icon"
                                        tag="a"
                                        :target="$subNavigationItem->shouldOpenUrlInNewTab() ? '_blank' : null"
                                    >
                                        {{ $subNavigationItem->getLabel() }}
                                    </x-filament::tabs.item>
                                @endforeach
                            @endif
                        @endforeach
                    </x-filament::tabs>
                @endif
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

            @if ($subNavigation && $subNavigationPosition === SubNavigationPosition::End)
                <x-filament-panels::page.sub-navigation
                    :navigation="$subNavigation"
                />
            @endif
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
