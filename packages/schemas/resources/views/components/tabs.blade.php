@php
    use Filament\Schemas\Components\Tabs\Tab;
    use Filament\Schemas\View\SchemaIconAlias;
    use Filament\Support\Icons\Heroicon;

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
            value="{{ collect($tabs)->filter(static fn (Tab $tab): bool => $tab->isVisible())->map(static fn (Tab $tab) => $tab->getKey(isAbsolute: false))->values()->toJson() }}"
            x-ref="tabsData"
        />

        <x-filament::tabs
            :contained="$isContained"
            :label="$label"
            :vertical="$isVertical"
            x-cloak
            :x-bind:class="! $isScrollable ? '{ \'fi-invisible\': ! withinDropdownMounted }' : null"
        >
            @foreach ($getStartRenderHooks() as $startRenderHook)
                {{ \Filament\Support\Facades\FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes) }}
            @endforeach

            @foreach ($tabs as $index => $tab)
                @php
                    $tabBadge = $tab->getBadge();
                    $tabBadgeColor = $tab->getBadgeColor();
                    $tabBadgeIcon = $tab->getBadgeIcon();
                    $tabBadgeIconPosition = $tab->getBadgeIconPosition();
                    $tabBadgeTooltip = $tab->getBadgeTooltip();
                    $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                    $tabIcon = $tab->getIcon();
                    $tabIconPosition = $tab->getIconPosition();
                    $tabKey = $tab->getKey(isAbsolute: false);
                    $tabLabel = $tab->getLabel();
                    $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'inline');
                @endphp

                <x-filament::tabs.item
                    :alpine-active="'tab === \'' . $tabKey . '\''"
                    :attributes="$tabExtraAttributeBag"
                    :badge="$tabBadge"
                    :badge-color="$tabBadgeColor"
                    :badge-icon="$tabBadgeIcon"
                    :badge-icon-position="$tabBadgeIconPosition"
                    :badge-tooltip="$tabBadgeTooltip"
                    :data-tab-key="$tabKey"
                    :icon="$tabIcon"
                    :icon-position="$tabIconPosition"
                    :x-cloak="$tabVisibilityJs !== null"
                    :x-on:click="'tab = \'' . $tabKey . '\''"
                    :x-show="$tabVisibilityJs"
                >
                    {{ $tabLabel }}
                </x-filament::tabs.item>
            @endforeach

            @if (! $isScrollable)
                <x-filament::dropdown
                    :placement="__('filament-panels::layout.direction') === 'ltr' ? 'bottom-start' : 'bottom-end'"
                >
                    <x-slot name="trigger">
                        @foreach ($tabs as $index => $tab)
                            @php
                                $tabBadge = $tab->getBadge();
                                $tabBadgeColor = $tab->getBadgeColor();
                                $tabBadgeTooltip = $tab->getBadgeTooltip();
                                $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                                $tabKey = $tab->getKey(isAbsolute: false);
                                $tabLabel = $tab->getLabel();
                                $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'trigger');
                            @endphp

                            <x-filament::tabs.item
                                :alpine-active="'tab === \'' . $tabKey . '\''"
                                :attributes="$tabExtraAttributeBag"
                                :badge="$tabBadge"
                                :badge-color="$tabBadgeColor"
                                :badge-tooltip="$tabBadgeTooltip"
                                :icon="Heroicon::ChevronDown"
                                :icon-alias="SchemaIconAlias::COMPONENTS_TABS_DROPDOWN_TRIGGER_BUTTON"
                                :x-cloak="$tabVisibilityJs !== null"
                                :x-show="$tabVisibilityJs"
                            >
                                {{ $tabLabel }}
                            </x-filament::tabs.item>
                        @endforeach

                        <x-filament::tabs.item x-show="isDropdownButtonVisible">
                            {{
                                \Filament\Support\generate_icon_html(
                                    Heroicon::EllipsisHorizontal,
                                    alias: SchemaIconAlias::COMPONENTS_TABS_MORE_TABS_BUTTON,
                                )
                            }}
                        </x-filament::tabs.item>
                    </x-slot>

                    <x-filament::dropdown.list>
                        @foreach ($tabs as $index => $tab)
                            @php
                                $tabBadge = $tab->getBadge();
                                $tabBadgeColor = $tab->getBadgeColor();
                                $tabBadgeTooltip = $tab->getBadgeTooltip();
                                $tabIcon = $tab->getIcon();
                                $tabKey = $tab->getKey(isAbsolute: false);
                                $tabLabel = $tab->getLabel();
                            @endphp

                            <x-filament::dropdown.list.item
                                :badge="$tabBadge"
                                :badge-color="$tabBadgeColor"
                                :badge-tooltip="$tabBadgeTooltip"
                                :icon="$tabIcon"
                                x-bind:class="{ 'fi-selected': tab === '{{ $tabKey }}' }"
                                :x-on:click="'tab = \'' . $tabKey . '\'; close($event);'"
                                :x-show="$index . ' >= withinDropdownIndex'"
                            >
                                {{ $tabLabel }}
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
                    $tabBadge = $tab->getBadge();
                    $tabBadgeColor = $tab->getBadgeColor();
                    $tabBadgeIcon = $tab->getBadgeIcon();
                    $tabBadgeIconPosition = $tab->getBadgeIconPosition();
                    $tabBadgeTooltip = $tab->getBadgeTooltip();
                    $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                    $tabIcon = $tab->getIcon();
                    $tabIconPosition = $tab->getIconPosition();
                    $tabKey = strval($tabKey);
                    $tabLabel = $tab->getLabel() ?? $this->generateTabLabel($tabKey);
                @endphp

                <x-filament::tabs.item
                    :active="$activeTab === $tabKey"
                    :attributes="$tabExtraAttributeBag"
                    :badge="$tabBadge"
                    :badge-color="$tabBadgeColor"
                    :badge-icon="$tabBadgeIcon"
                    :badge-icon-position="$tabBadgeIconPosition"
                    :badge-tooltip="$tabBadgeTooltip"
                    :icon="$tabIcon"
                    :icon-position="$tabIconPosition"
                    :wire:click="'$set(\'' . $livewireProperty . '\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                >
                    {{ $tabLabel }}
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
