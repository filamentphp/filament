@php
    use Filament\Schemas\Components\Tabs\Tab;
    use Filament\Support\Enums\IconPosition;

    $activeTab = $getActiveTab();
    $id = $getId();
    $isContained = $isContained();
    $isScrollable = $isScrollable();
    $isVertical = $isVertical();
    $label = $getLabel();
    $livewireProperty = $getLivewireProperty();
    $renderHookScopes = $getRenderHookScopes();
    $tabs = $getChildSchema()->getComponents();

    $getTabVisibilityJs = function (Tab $tab, ?int $index = null, ?string $mode = null) use ($isScrollable): ?string {
        $hiddenJs = $tab->getHiddenJs();
        $visibleJs = $tab->getVisibleJs();

        $baseJs = match ([filled($hiddenJs), filled($visibleJs)]) {
            [true, true] => "(! ({$hiddenJs})) && ({$visibleJs})",
            [true, false] => "! ({$hiddenJs})",
            [false, true] => $visibleJs,
            default => null,
        };

        if ($isScrollable || $index === null || $mode === null) {
            return $baseJs;
        }

        $tabKey = $tab->getKey(isAbsolute: false);

        $dropdownJs = match ($mode) {
            'inline' => "(!withinDropdownMounted || withinDropdownIndex === null || {$index} < withinDropdownIndex)",
            'trigger' => "(withinDropdownMounted && withinDropdownIndex !== null && {$index} >= withinDropdownIndex && '{$tabKey}' === tab)",
            default => null,
        };

        return $baseJs ? "{$baseJs} && {$dropdownJs}" : $dropdownJs;
    };
@endphp

@if (blank($livewireProperty))
    <div
        @if (! $isScrollable)
            @resize.window="updateTabsWithinDropdown()"
        @endif
        x-data="tabsSchemaComponent({
            activeTab: @js($activeTab),
            isScrollable: @js($isScrollable),
            isTabPersistedInQueryString: @js($isTabPersistedInQueryString()),
            livewireId: @js($this->getId()),
            tab: @if ($isTabPersisted() && filled($id)) $persist(null).as(@js($id)) @else @js(null) @endif,
            tabQueryStringKey: @js($getTabQueryStringKey()),
        })"
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tabs', 'filament/schemas') }}"
        wire:ignore.self
        {{
            $attributes
                ->merge([
                    'id' => $id,
                    'wire:key' => $getLivewireKey() . '.container',
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-sc-tabs',
                    'fi-contained' => $isContained,
                    'fi-vertical' => $isVertical,
                ])
        }}
    >
        <input
            type="hidden"
            value="{{ collect($tabs)->filter(fn (Tab $tab): bool => $tab->isVisible())->map(fn (Tab $tab) => $tab->getKey(isAbsolute: false))->values()->toJson() }}"
            x-ref="tabsData"
        />

        <x-filament::tabs
            :contained="$isContained"
            :label="$label"
            :vertical="$isVertical"
            x-cloak
        >
            @foreach ($getStartRenderHooks() as $startRenderHook)
                {{ \Filament\Support\Facades\FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes) }}
            @endforeach

            @foreach ($tabs as $index => $tab)
                @php
                    $tabKey = $tab->getKey(isAbsolute: false);
                    $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'inline');
                @endphp

                <x-filament::tabs.item
                    :alpine-active="'tab === \'' . $tabKey . '\''"
                    :attributes="$tab->getExtraAttributeBag()"
                    :badge="$tab->getBadge()"
                    :badge-color="$tab->getBadgeColor()"
                    :badge-icon="$tab->getBadgeIcon()"
                    :badge-icon-position="$tab->getBadgeIconPosition()"
                    :badge-tooltip="$tab->getBadgeTooltip()"
                    :data-tab-key="$tabKey"
                    :icon="$tab->getIcon()"
                    :icon-position="$tab->getIconPosition()"
                    :x-cloak="$tabVisibilityJs !== null"
                    :x-on:click="'tab = \'' . $tabKey . '\''"
                    :x-show="$tabVisibilityJs"
                >
                    {{ $tab->getLabel() }}
                </x-filament::tabs.item>
            @endforeach

            @if (! $isScrollable)
                <x-filament::dropdown
                    :placement="__('filament-panels::layout.direction') === 'ltr' ? 'bottom-start' : 'bottom-end'"
                >
                    <x-slot name="trigger">
                        @foreach ($tabs as $index => $tab)
                            @php
                                $tabKey = $tab->getKey(isAbsolute: false);
                                $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'trigger');
                            @endphp

                            <x-filament::tabs.item
                                :alpine-active="'tab === \'' . $tabKey . '\''"
                                :attributes="$tab->getExtraAttributeBag()"
                                icon="heroicon-o-chevron-down"
                                :icon-position="IconPosition::After"
                                :x-cloak="$tabVisibilityJs !== null"
                                :x-show="$tabVisibilityJs"
                            >
                                {{ $tab->getLabel() }}
                            </x-filament::tabs.item>
                        @endforeach

                        <x-filament::tabs.item x-show="isDropdownButtonVisible">
                            <x-filament::icon
                                icon="heroicon-c-ellipsis-horizontal"
                            />
                        </x-filament::tabs.item>
                    </x-slot>

                    <x-filament::dropdown.list>
                        @foreach ($tabs as $index => $tab)
                            @php
                                $tabKey = $tab->getKey(isAbsolute: false);
                            @endphp

                            <x-filament::dropdown.list.item
                                :icon="$tab->getIcon()"
                                x-bind:class="{ 'fi-active': tab === '{{ $tabKey }}' }"
                                :x-on:click="'tab = \'' . $tabKey . '\'; close($event);'"
                                :x-show="$index . ' >= withinDropdownIndex'"
                            >
                                {{ $tab->getLabel() }}
                            </x-filament::dropdown.list.item>
                        @endforeach
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            @endif

            @foreach ($getEndRenderHooks() as $endRenderHook)
                {{ \Filament\Support\Facades\FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes) }}
            @endforeach
        </x-filament::tabs>

        @foreach ($tabs as $tab)
            @php
                $tabVisibilityJs = $getTabVisibilityJs($tab);
            @endphp

            @if ($tabVisibilityJs)
                <div x-cloak x-show="{!! $tabVisibilityJs !!}">
                    {{ $tab }}
                </div>
            @else
                {{ $tab }}
            @endif
        @endforeach
    </div>
@else
    @php
        $activeTab = strval($this->{$livewireProperty});
    @endphp

    <div
        {{
            $attributes
                ->merge([
                    'id' => $id,
                    'wire:key' => $getLivewireKey() . '.container',
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-sc-tabs',
                    'fi-contained' => $isContained,
                    'fi-vertical' => $isVertical,
                ])
        }}
    >
        <x-filament::tabs
            :contained="$isContained"
            :label="$label"
            :vertical="$isVertical"
        >
            @foreach ($getStartRenderHooks() as $startRenderHook)
                {{ \Filament\Support\Facades\FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes) }}
            @endforeach

            @foreach ($getChildSchema()->getComponents(withOriginalKeys: true) as $tabKey => $tab)
                @php
                    $tabKey = strval($tabKey);
                @endphp

                <x-filament::tabs.item
                    :active="$activeTab === $tabKey"
                    :attributes="$tab->getExtraAttributeBag()"
                    :badge="$tab->getBadge()"
                    :badge-color="$tab->getBadgeColor()"
                    :badge-icon="$tab->getBadgeIcon()"
                    :badge-icon-position="$tab->getBadgeIconPosition()"
                    :badge-tooltip="$tab->getBadgeTooltip()"
                    :icon="$tab->getIcon()"
                    :icon-position="$tab->getIconPosition()"
                    :wire:click="'$set(\'' . $livewireProperty . '\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                >
                    {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                </x-filament::tabs.item>
            @endforeach

            @foreach ($getEndRenderHooks() as $endRenderHook)
                {{ \Filament\Support\Facades\FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes) }}
            @endforeach
        </x-filament::tabs>

        @foreach ($getChildSchema()->getComponents(withOriginalKeys: true) as $tabKey => $tab)
            {{ $tab->key($tabKey) }}
        @endforeach
    </div>
@endif
