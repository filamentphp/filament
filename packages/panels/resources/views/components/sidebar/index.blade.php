@props([
    'navigation',
])

@php
    $openSidebarClasses = 'fi-sidebar-open max-w-none translate-x-0 shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10';
@endphp

<aside
    x-data="{}"
    @if (filament()->isSidebarCollapsibleOnDesktop())
        x-cloak
        x-bind:class="
            $store.sidebar.isOpen
                ? @js($openSidebarClasses)
                : 'lg:max-w-[--collapsed-sidebar-width] -translate-x-full rtl:translate-x-full lg:translate-x-0'
        "
    @else
        @if (filament()->hasTopNavigation() || filament()->isSidebarFullyCollapsibleOnDesktop())
            x-cloak
        @else
            x-cloak="-lg"
        @endif
        x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses) : '-translate-x-full rtl:translate-x-full'"
    @endif
    @class([
        'fi-sidebar fixed inset-y-0 start-0 z-30 grid h-screen w-[--sidebar-width] content-start overflow-hidden bg-white transition-all dark:bg-gray-900 lg:z-0 lg:bg-transparent lg:shadow-none lg:ring-0 dark:lg:bg-transparent',
        'lg:translate-x-0 rtl:lg:-translate-x-0' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()),
        'lg:-translate-x-full rtl:lg:translate-x-full' => filament()->hasTopNavigation(),
    ])
>
    <header
        class="fi-sidebar-header flex h-16 items-center bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:shadow-sm"
    >
        {{-- format-ignore-start --}}
        <div
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-show="$store.sidebar.isOpen"
                x-transition:enter="lg:transition lg:delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            @endif
        >
            @if ($homeUrl = filament()->getHomeUrl())
                <a href="{{ $homeUrl }}">
                    <x-filament-panels::logo />
                </a>
            @else
                <x-filament-panels::logo />
            @endif
        </div>
        {{-- format-ignore-end --}}

        @if (filament()->isSidebarCollapsibleOnDesktop())
            <x-filament::icon-button
                color="gray"
                icon="heroicon-o-chevron-right"
                icon-alias="panels::sidebar.expand-button"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                class="-mx-1.5"
            />
        @endif

        @if (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop())
            <x-filament::icon-button
                color="gray"
                icon="heroicon-o-chevron-left"
                icon-alias="panels::sidebar.collapse-button"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen"
                class="-mx-1.5 ms-auto hidden lg:flex"
            />
        @endif
    </header>

    <nav
        class="fi-sidebar-nav grid gap-y-7 overflow-y-auto overflow-x-hidden px-6 py-8"
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.nav.start') }}

        @if (filament()->hasTenancy())
            <div
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-bind:class="$store.sidebar.isOpen ? '-mx-2' : '-mx-4'"
                @endif
            >
                <x-filament-panels::tenant-menu />
            </div>
        @endif

        @if (filament()->hasNavigation())
            <ul
                class="-mx-2 grid gap-y-7"
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-bind:class="{ 'auto-cols-min': ! $store.sidebar.isOpen }"
                @endif
            >
                @foreach ($navigation as $group)
                    <x-filament-panels::sidebar.group
                        :collapsible="$group->isCollapsible()"
                        :icon="$group->getIcon()"
                        :items="$group->getItems()"
                        :label="$group->getLabel()"
                    />
                @endforeach
            </ul>

            @php
                $collapsedNavigationGroupLabels = collect($navigation)
                    ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                    ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                    ->values();
            @endphp

            <script>
                let collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                if (collapsedGroups === null || collapsedGroups === 'null') {
                    localStorage.setItem(
                        'collapsedGroups',
                        JSON.stringify(@js($collapsedNavigationGroupLabels)),
                    )
                }

                collapsedGroups = JSON.parse(
                    localStorage.getItem('collapsedGroups'),
                )

                document
                    .querySelectorAll('.fi-sidebar-group')
                    .forEach((group) => {
                        if (
                            !collapsedGroups.includes(group.dataset.groupLabel)
                        ) {
                            return
                        }

                        // Alpine.js loads too slow, so attempt to hide a
                        // collapsed sidebar group earlier.
                        group.querySelector(
                            '.fi-sidebar-group-items',
                        ).style.display = 'none'
                        group
                            .querySelector('.fi-sidebar-group-collapse-button')
                            .classList.add('rotate-180')
                    })
            </script>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.nav.end') }}
    </nav>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::sidebar.footer') }}
</aside>
