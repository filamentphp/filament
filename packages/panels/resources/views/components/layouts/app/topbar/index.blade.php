@props([
    'breadcrumbs' => [],
    'navigation',
])

<header
    {{ $attributes->class(['filament-main-topbar sticky top-0 z-10 border-b bg-white dark:border-gray-700 dark:bg-gray-800']) }}
>
    <div
        class="-mt-px flex h-16 items-center justify-between px-2 sm:px-4 md:px-6 lg:px-8"
    >
        <div class="flex flex-1 items-center">
            {{ filament()->renderHook('topbar.start') }}

            <button
                x-cloak
                x-data="{}"
                x-bind:aria-label="$store.sidebar.isOpen ? '{{ __('filament::layout.buttons.sidebar.collapse.label') }}' : '{{ __('filament::layout.buttons.sidebar.expand.label') }}'"
                x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                @class([
                    'filament-sidebar-open-button flex h-10 w-10 shrink-0 items-center justify-center rounded-full outline-none hover:bg-gray-500/5 focus:bg-primary-500/10',
                    'lg:me-4' => filament()->isSidebarFullyCollapsibleOnDesktop(),
                    'lg:hidden' => ! (filament()->isSidebarFullyCollapsibleOnDesktop()),
                ])
            >
                <x-filament::icon
                    name="heroicon-o-bars-3"
                    alias="panels::topbar.buttons.toggle-sidebar"
                    color="text-primary-500"
                    size="h-6 w-6"
                />
            </button>

            @if (filament()->hasTopNavigation())
                <div class="me-12 hidden lg:flex">
                    @if ($homeUrl = filament()->getHomeUrl())
                        <a href="{{ $homeUrl }}" data-turbo="false">
                            <x-filament::logo />
                        </a>
                    @else
                        <x-filament::logo />
                    @endif
                </div>

                @if (filament()->hasNavigation())
                    <ul class="hidden flex-wrap items-center gap-3 lg:flex">
                        @foreach ($navigation as $group)
                            @if ($groupLabel = $group->getLabel())
                                <x-filament::dropdown placement="bottom-start">
                                    <x-slot name="trigger">
                                        <x-filament::layouts.app.topbar.item
                                            :active="$group->isActive()"
                                            :icon="$group->getIcon()"
                                        >
                                            {{ $groupLabel }}
                                        </x-filament::layouts.app.topbar.item>
                                    </x-slot>

                                    <x-filament::dropdown.list>
                                        @foreach ($group->getItems() as $item)
                                            <x-filament::dropdown.list.item
                                                :icon="$item->isActive() ? ($item->getActiveIcon() ?? $item->getIcon()) : $item->getIcon()"
                                                :href="$item->getUrl()"
                                                :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null"
                                                tag="a"
                                            >
                                                {{ $item->getLabel() }}
                                            </x-filament::dropdown.list.item>
                                        @endforeach
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            @else
                                @foreach ($group->getItems() as $item)
                                    <x-filament::layouts.app.topbar.item
                                        :active="$item->isActive()"
                                        :icon="$item->getIcon()"
                                        :active-icon="$item->getActiveIcon()"
                                        :url="$item->getUrl()"
                                        :badge="$item->getBadge()"
                                        :badgeColor="$item->getBadgeColor()"
                                        :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                                    >
                                        {{ $item->getLabel() }}
                                    </x-filament::layouts.app.topbar.item>
                                @endforeach
                            @endif
                        @endforeach
                    </ul>
                @endif
            @elseif (count($breadcrumbs))
                <x-filament::layouts.app.topbar.breadcrumbs
                    :breadcrumbs="$breadcrumbs"
                />
            @endif
        </div>

        <div class="flex items-center">
            @if (filament()->getGlobalSearchProvider() !== null)
                @livewire('filament.core.global-search')
            @endif

            @if (filament()->hasDatabaseNotifications())
                @livewire('filament.core.database-notifications')
            @endif

            <x-filament::user-menu />

            {{ filament()->renderHook('topbar.end') }}
        </div>
    </div>

    @if (filament()->hasTopNavigation() && count($breadcrumbs))
        <div
            class="hidden h-12 items-center border-t px-8 dark:border-gray-700 lg:flex"
        >
            <x-filament::layouts.app.topbar.breadcrumbs
                :breadcrumbs="$breadcrumbs"
            />
        </div>
    @endif
</header>
