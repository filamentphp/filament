<aside
    x-data="{}"
    x-cloak="-lg"
    x-bind:class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @class([
        'fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-20 flex flex-col h-screen overflow-hidden shadow-2xl transition-transform bg-white filament-sidebar lg:border-r w-80 lg:z-0 lg:translate-x-0',
        'dark:bg-gray-800 dark:border-gray-700' => config('filament.dark_mode'),
    ])
>
    <header @class([
        'border-b h-[4rem] shrink-0 px-6 flex items-center filament-sidebar-header',
        'dark:border-gray-700' => config('filament.dark_mode'),
    ])>
        <a href="{{ config('filament.home_url') }}">
            <x-filament::brand />
        </a>
    </header>

    <nav class="flex-1 overflow-y-auto py-6 filament-sidebar-nav">
        <x-filament::layouts.app.sidebar.start />

        <ul class="space-y-6 px-6">
            @foreach (\Filament\Facades\Filament::getNavigation() as $group => $items)
                <x-filament::layouts.app.sidebar.group :label="$group">
                    @foreach ($items as $item)
                        <x-filament::layouts.app.sidebar.item
                            :active="$item->isActive()"
                            :icon="$item->getIcon()"
                            :url="$item->getUrl()"
                            :badge="$item->getBadge()"
                        >
                            {{ $item->getLabel() }}
                        </x-filament::layouts.app.sidebar.item>
                    @endforeach
                </x-filament::layouts.app.sidebar.group>

                @if (! $loop->last)
                    <li>
                        <div @class([
                            'border-t -mr-6',
                            'dark:border-gray-700' => config('filament.dark_mode'),
                        ])></div>
                    </li>
                @endif
            @endforeach
        </ul>

        <x-filament::layouts.app.sidebar.end />
    </nav>

    <x-filament::layouts.app.sidebar.footer />
</aside>
