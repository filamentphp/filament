<aside
    x-data="{}"
    x-cloak
    x-bind:class="$store.sidebar.isOpen ? 'translate-x-0' : '-translate-x-full'"
    class="{{ config("filament.styles.sidebar_container") }}"
>
    <header class="border-b h-[4rem] flex-shrink-0 px-6 flex items-center">
        <a href="{{ \Filament\Facades\Filament::geturl() }}">
            <x-filament::brand />
        </a>
    </header>

    <nav class="flex-1 overflow-y-auto py-6">
        <x-filament::layouts.app.sidebar.start />

        <ul class="space-y-6 px-6">
            @foreach (\Filament\Facades\Filament::getNavigation() as $group => $items)
                <x-filament::layouts.app.sidebar.group :label="$group">
                    @foreach ($items as $item)
                        <x-filament::layouts.app.sidebar.item
                            :active="$item->isActive()"
                            :icon="$item->getIcon()"
                            :url="$item->getUrl()"
                        >
                            {{ $item->getLabel() }}
                        </x-filament::layouts.app.sidebar.item>
                    @endforeach
                </x-filament::layouts.app.sidebar.group>

                @if (! $loop->last)
                    <li>
                        <div class="border-t -mr-6"></div>
                    </li>
                @endif
            @endforeach
        </ul>

        <x-filament::layouts.app.sidebar.end />
    </nav>

    <x-filament::layouts.app.sidebar.footer />
</aside>
