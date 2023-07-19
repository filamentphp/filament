@props([
    'navigation',
])

<header
    {{ $attributes->class(['fi-topbar sticky top-0 z-20 flex h-16 items-center justify-between bg-white px-2 shadow-[0_1px_0_0_theme(colors.gray.950_/_5%)] dark:bg-gray-900 dark:shadow-[0_1px_0_0_theme(colors.white_/_10%)] sm:px-4 md:px-6 lg:px-8']) }}
>
    {{ \Filament\Support\Facades\FilamentView::renderHook('topbar.start') }}

    <button
        x-cloak
        x-data="{}"
        x-bind:aria-label="
            $store.sidebar.isOpen
                ? '{{ __('filament::layout.actions.sidebar.collapse.label') }}'
                : '{{ __('filament::layout.actions.sidebar.expand.label') }}'
        "
        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
        @class([
            'fi-sidebar-open-btn flex h-10 w-10 shrink-0 items-center justify-center rounded-full outline-none hover:bg-gray-500/5 focus:bg-primary-500/10',
            'lg:me-4' => filament()->isSidebarFullyCollapsibleOnDesktop(),
            'lg:hidden' => ! (filament()->isSidebarFullyCollapsibleOnDesktop()),
        ])
    >
        <x-filament::icon
            name="heroicon-o-bars-3"
            alias="panels::topbar.open-mobile-sidebar-button"
            class="h-6 w-6 text-primary-500"
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
            <ul class="hidden flex-wrap items-center gap-x-1 lg:flex">
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
    @endif

    <div x-persist="topbar.end" class="ms-auto flex items-center gap-x-4">
        @if (filament()->isGlobalSearchEnabled())
            @livewire(Filament\Livewire\GlobalSearch::class, ['lazy' => true])
        @endif

        @if (filament()->hasDatabaseNotifications())
            @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
        @endif

        <x-filament::user-menu />
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook('topbar.end') }}
</header>
