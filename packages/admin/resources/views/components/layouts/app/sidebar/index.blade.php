<aside
    x-data="{}"
    @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
        x-cloak
        x-bind:class="
            $store.sidebar.isOpen
                ? 'filament-sidebar-open translate-x-0 max-w-[20em] shadow-2xl lg:max-w-[var(--sidebar-width)]'
                : '-translate-x-full lg:translate-x-0 lg:max-w-[var(--collapsed-sidebar-width)] lg:shadow-2xl rtl:lg:-translate-x-0 rtl:translate-x-full'
        "
    @else
        x-cloak="-lg"
        x-bind:class="
            $store.sidebar.isOpen
                ? 'filament-sidebar-open translate-x-0 shadow-2xl'
                : '-translate-x-full lg:translate-x-0 lg:shadow-2xl rtl:lg:-translate-x-0 rtl:translate-x-full'
        "
    @endif
    @class([
        'filament-sidebar fixed inset-y-0 left-0 z-20 flex h-screen w-[var(--sidebar-width)] flex-col overflow-hidden bg-white transition-all rtl:left-auto rtl:right-0 lg:z-0 lg:border-r rtl:lg:border-l rtl:lg:border-r-0',
        'lg:translate-x-0' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
        'dark:border-gray-700 dark:bg-gray-800' => config('filament.dark_mode'),
    ])
>
    <header
        @class([
            'filament-sidebar-header relative flex h-[4rem] shrink-0 items-center justify-center border-b',
            'dark:border-gray-700' => config('filament.dark_mode'),
        ])
    >
        <div
            @class([
                'flex w-full items-center justify-center px-6',
                'lg:px-4' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
            ])
            x-show="$store.sidebar.isOpen || @js(! config('filament.layout.sidebar.is_collapsible_on_desktop')) || @js(config('filament.layout.sidebar.collapsed_width') === 0)"
            x-transition:enter="delay-100 lg:transition"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0))
                <button
                    type="button"
                    class="filament-sidebar-collapse-button hidden h-10 w-10 shrink-0 items-center justify-center rounded-full text-primary-500 outline-none hover:bg-gray-500/5 focus:bg-primary-500/10 lg:flex"
                    x-bind:aria-label="
                        $store.sidebar.isOpen
                            ? '{{ __('filament::layout.buttons.sidebar.collapse.label') }}'
                            : '{{ __('filament::layout.buttons.sidebar.expand.label') }}'
                    "
                    x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    x-transition:enter="delay-100 lg:transition"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    <svg
                        class="h-6 w-6 rtl:scale-x-[-1]"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M20.25 7.5L16 12L20.25 16.5M3.75 12H12M3.75 17.25H16M3.75 6.75H16"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>
            @endif

            <div
                data-turbo="false"
                @class([
                    'relative flex w-full items-center',
                    'lg:ml-3' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
                ])
            >
                <a
                    href="{{ config('filament.home_url') }}"
                    class="inline-block"
                >
                    <x-filament::brand />
                </a>
            </div>
        </div>

        @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
            <button
                type="button"
                class="filament-sidebar-close-button flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-primary-500 outline-none hover:bg-gray-500/5 focus:bg-primary-500/10"
                x-bind:aria-label="
                    $store.sidebar.isOpen
                        ? '{{ __('filament::layout.buttons.sidebar.collapse.label') }}'
                        : '{{ __('filament::layout.buttons.sidebar.expand.label') }}'
                "
                x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                x-show="! $store.sidebar.isOpen && @js(config('filament.layout.sidebar.collapsed_width') !== 0)"
                x-transition:enter="delay-100 lg:transition"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            >
                <svg
                    class="h-6 w-6"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    />
                </svg>
            </button>
        @endif
    </header>

    <nav
        class="filament-sidebar-nav flex-1 overflow-y-auto overflow-x-hidden py-6"
    >
        <x-filament::layouts.app.sidebar.start />
        {{ \Filament\Facades\Filament::renderHook('sidebar.start') }}

        @php
            $navigation = \Filament\Facades\Filament::getNavigation();

            $collapsedNavigationGroupLabels = collect($navigation)
                ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                ->values();
        @endphp

        <script>
            if (
                JSON.parse(localStorage.getItem('collapsedGroups')) === null ||
                JSON.parse(localStorage.getItem('collapsedGroups')) === 'null'
            ) {
                localStorage.setItem(
                    'collapsedGroups',
                    JSON.stringify(@js($collapsedNavigationGroupLabels)),
                )
            }
        </script>

        <ul class="space-y-6 px-6">
            @foreach ($navigation as $group)
                <x-filament::layouts.app.sidebar.group
                    :label="$group->getLabel()"
                    :icon="$group->getIcon()"
                    :collapsible="$group->isCollapsible()"
                    :items="$group->getItems()"
                />

                @if (! $loop->last)
                    <li>
                        <div
                            @class([
                                'rtl:-mr-auto -mr-6 border-t rtl:-ml-6',
                                'dark:border-gray-700' => config('filament.dark_mode'),
                            ])
                        ></div>
                    </li>
                @endif
            @endforeach
        </ul>

        <x-filament::layouts.app.sidebar.end />
        {{ \Filament\Facades\Filament::renderHook('sidebar.end') }}
    </nav>

    <x-filament::layouts.app.sidebar.footer />
</aside>
