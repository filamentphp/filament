@php
    use Filament\Enums\DatabaseNotificationsPosition;
    use Filament\Enums\GlobalSearchPosition;
    use Filament\Enums\UserMenuPosition;
    use Filament\Livewire\GlobalSearch;
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use Filament\View\PanelsIconAlias;
    use Filament\View\PanelsRenderHook;
@endphp

<div class="fi-topbar-ctn">
    @php
        $isRtl = __('filament-panels::layout.direction') === 'rtl';
        $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
        $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
        $hasTopNavigation = filament()->hasTopNavigation();
        $hasNavigation = filament()->hasNavigation();
        $hasTenancy = filament()->hasTenancy();
    @endphp

    <nav
        aria-label="{{ __('filament-panels::layout.topbar.label') }}"
        class="fi-topbar"
    >
        {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_START) }}

        @if ($hasNavigation)
            <x-filament::icon-button
                color="gray"
                :icon="Heroicon::OutlinedBars3"
                :icon-alias="PanelsIconAlias::TOPBAR_OPEN_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                aria-controls="fi-main-sidebar"
                x-bind:aria-expanded="$store.sidebar.isOpen"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                class="fi-topbar-open-sidebar-btn"
            />

            <x-filament::icon-button
                color="gray"
                :icon="Heroicon::OutlinedXMark"
                :icon-alias="PanelsIconAlias::TOPBAR_CLOSE_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                x-cloak
                x-data="{}"
                aria-controls="fi-main-sidebar"
                x-bind:aria-expanded="$store.sidebar.isOpen"
                x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen"
                class="fi-topbar-close-sidebar-btn"
            />
        @endif

        <div class="fi-topbar-start">
            @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
                <div
                    x-show="$store.sidebar.isOpen || @js($isSidebarCollapsibleOnDesktop)"
                    class="fi-topbar-collapse-sidebar-btn-ctn"
                >
                    @if ($isSidebarCollapsibleOnDesktop)
                        <x-filament::icon-button
                            color="gray"
                            :icon="$isRtl ? Heroicon::OutlinedChevronLeft : Heroicon::OutlinedChevronRight"
                            {{-- @deprecated Use `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL` instead of `PanelsIconAlias::SIDEBAR_EXPAND_BUTTON` for RTL. --}}
                            :icon-alias="
                                $isRtl
                                ? [
                                    PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL,
                                    PanelsIconAlias::SIDEBAR_EXPAND_BUTTON,
                                ]
                                : PanelsIconAlias::SIDEBAR_EXPAND_BUTTON
                            "
                            icon-size="lg"
                            :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                            x-cloak
                            x-data="{}"
                            aria-controls="fi-main-sidebar"
                            x-bind:aria-expanded="$store.sidebar.isOpen"
                            x-on:click="$store.sidebar.open()"
                            x-show="! $store.sidebar.isOpen"
                            class="fi-topbar-open-collapse-sidebar-btn"
                        />
                    @endif

                    @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
                        <x-filament::icon-button
                            color="gray"
                            :icon="$isRtl ? Heroicon::OutlinedChevronRight : Heroicon::OutlinedChevronLeft"
                            {{-- @deprecated Use `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL` instead of `PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON` for RTL. --}}
                            :icon-alias="
                                $isRtl
                                ? [
                                    PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL,
                                    PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON,
                                ]
                                : PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON
                            "
                            icon-size="lg"
                            :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                            x-cloak
                            x-data="{}"
                            aria-controls="fi-main-sidebar"
                            x-bind:aria-expanded="$store.sidebar.isOpen"
                            x-on:click="$store.sidebar.close()"
                            x-show="$store.sidebar.isOpen"
                            class="fi-topbar-close-collapse-sidebar-btn"
                        />
                    @endif
                </div>
            @endif

            {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_BEFORE) }}

            @if ($homeUrl = filament()->getHomeUrl())
                <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                    <x-filament-panels::logo />
                </a>
            @else
                <x-filament-panels::logo />
            @endif

            {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER) }}
        </div>

        @if ($hasTopNavigation || (! $hasNavigation))
            @if ($hasTenancy && filament()->hasTenantMenu())
                <x-filament-panels::tenant-menu teleport />
            @endif

            @if ($hasNavigation)
                @php
                    $navigation = filament()->getNavigation();
                @endphp

                <ul class="fi-topbar-nav-groups">
                    @foreach ($navigation as $group)
                        @php
                            $groupLabel = $group->getLabel();
                            $groupExtraTopbarAttributeBag = $group->getExtraTopbarAttributeBag();
                            $isGroupActive = $group->isActive();
                            $groupIcon = $group->getIcon();
                        @endphp

                        @if ($groupLabel)
                            <x-filament::dropdown
                                placement="bottom-start"
                                teleport
                                :attributes="\Filament\Support\prepare_inherited_attributes($groupExtraTopbarAttributeBag)"
                            >
                                <x-slot name="trigger">
                                    <x-filament-panels::topbar.item
                                        :active="$isGroupActive"
                                        :icon="$groupIcon"
                                    >
                                        {{ $groupLabel }}
                                    </x-filament-panels::topbar.item>
                                </x-slot>

                                @php
                                    $lists = [];

                                    foreach ($group->getItems() as $item) {
                                        if ($childItems = $item->getChildItems()) {
                                            $lists[] = [
                                                $item,
                                                ...$childItems,
                                            ];
                                            $lists[] = [];

                                            continue;
                                        }

                                        if (empty($lists)) {
                                            $lists[] = [$item];

                                            continue;
                                        }

                                        $lists[count($lists) - 1][] = $item;
                                    }

                                    if (empty($lists[count($lists) - 1])) {
                                        array_pop($lists);
                                    }
                                @endphp

                                @foreach ($lists as $list)
                                    <x-filament::dropdown.list>
                                        @foreach ($list as $item)
                                            @php
                                                $isItemActive = $item->isActive();
                                                $itemBadge = $item->getBadge();
                                                $itemBadgeColor = $item->getBadgeColor($itemBadge);
                                                $itemBadgeTooltip = $item->getBadgeTooltip($itemBadge);
                                                $itemUrl = $item->getUrl();
                                                $itemIcon = $isItemActive ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon();
                                                $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                                $itemExtraAttributes = $item->getExtraAttributeBag();
                                            @endphp

                                            <x-filament::dropdown.list.item
                                                :badge="$itemBadge"
                                                :badge-color="$itemBadgeColor"
                                                :badge-tooltip="$itemBadgeTooltip"
                                                :color="$isItemActive ? 'primary' : 'gray'"
                                                :href="$itemUrl"
                                                :icon="$itemIcon"
                                                tag="a"
                                                :target="$shouldItemOpenUrlInNewTab ? '_blank' : null"
                                                :aria-current="$isItemActive ? 'page' : null"
                                                :attributes="\Filament\Support\prepare_inherited_attributes($itemExtraAttributes)"
                                            >
                                                {{ $item->getLabel() }}
                                            </x-filament::dropdown.list.item>
                                        @endforeach
                                    </x-filament::dropdown.list>
                                @endforeach
                            </x-filament::dropdown>
                        @else
                            @foreach ($group->getItems() as $item)
                                @php
                                    $isItemActive = $item->isActive();
                                    $itemActiveIcon = $item->getActiveIcon();
                                    $itemBadge = $item->getBadge();
                                    $itemBadgeColor = $item->getBadgeColor($itemBadge);
                                    $itemBadgeTooltip = $item->getBadgeTooltip($itemBadge);
                                    $itemIcon = $item->getIcon();
                                    $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                    $itemUrl = $item->getUrl();
                                    $itemExtraAttributes = $item->getExtraAttributeBag();
                                @endphp

                                <x-filament-panels::topbar.item
                                    :active="$isItemActive"
                                    :active-icon="$itemActiveIcon"
                                    :badge="$itemBadge"
                                    :badge-color="$itemBadgeColor"
                                    :badge-tooltip="$itemBadgeTooltip"
                                    :icon="$itemIcon"
                                    :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                    :url="$itemUrl"
                                    :attributes="\Filament\Support\prepare_inherited_attributes($itemExtraAttributes)"
                                >
                                    {{ $item->getLabel() }}
                                </x-filament-panels::topbar.item>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @endif
        @endif

        <div
            @if ($hasTenancy)
                x-persist="topbar.end.panel-{{ filament()->getId() }}.tenant-{{ filament()->getTenant()?->getKey() }}"
            @else
                x-persist="topbar.end.panel-{{ filament()->getId() }}"
            @endif
            class="fi-topbar-end"
        >
            {{ FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_BEFORE) }}

            @if (filament()->isGlobalSearchEnabled() && filament()->getGlobalSearchPosition() === GlobalSearchPosition::Topbar)
                @livewire(GlobalSearch::class)
            @endif

            {{ FilamentView::renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER) }}

            @if (filament()->auth()->check())
                @if (filament()->hasDatabaseNotifications() && filament()->getDatabaseNotificationsPosition() === DatabaseNotificationsPosition::Topbar)
                    @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                    ])
                @endif

                @if (filament()->hasUserMenu() && filament()->getUserMenuPosition() === UserMenuPosition::Topbar)
                    <x-filament-panels::user-menu />
                @endif
            @endif
        </div>

        {{ FilamentView::renderHook(PanelsRenderHook::TOPBAR_END) }}
    </nav>

    <x-filament-actions::modals />
</div>
