@php
    $user = filament()->auth()->user();
    $items = filament()->getUserMenuItems();

    $accountItem = $items['account'] ?? null;
    $accountItemUrl = $accountItem?->getUrl();

    $logoutItem = $items['logout'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['account', 'logout']);
@endphp

{{ filament()->renderHook('user-menu.start') }}

<x-filament::dropdown placement="bottom-end" class="filament-user-menu">
    <x-slot name="trigger" class="ml-4 rtl:mr-4 rtl:ml-0">
        <button class="block" aria-label="{{ __('filament::layout.buttons.user_menu.label') }}">
            <x-filament::avatar.user :user="$user" />
        </button>
    </x-slot>

    {{ filament()->renderHook('user-menu.account.before') }}

    <x-filament::dropdown.header
        :color="$accountItem?->getColor() ?? 'gray'"
        :icon="$accountItem?->getIcon() ?? 'heroicon-m-user-circle'"
        :href="$accountItemUrl"
        :tag="filled($accountItemUrl) ? 'a' : 'div'"
    >
        {{ $accountItem?->getLabel() ?? filament()->getUserName($user) }}
    </x-filament::dropdown.header>

    {{ filament()->renderHook('user-menu.account.after') }}

    <x-filament::dropdown.list
        x-data="{
            mode: null,

            theme: null,

            init: function () {
                this.theme = localStorage.getItem('theme') || (this.isSystemDark() ? 'dark' : 'light')
                this.mode = localStorage.getItem('theme') ? 'manual' : 'auto'

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                    if (this.mode === 'manual') return

                    if (event.matches && (! document.documentElement.classList.contains('dark'))) {
                        this.theme = 'dark'

                        document.documentElement.classList.add('dark')
                    } else if ((! event.matches) && document.documentElement.classList.contains('dark')) {
                        this.theme = 'light'

                        document.documentElement.classList.remove('dark')
                    }
                })

                $watch('theme', () => {
                    if (this.mode === 'auto') return

                    localStorage.setItem('theme', this.theme)

                    if (this.theme === 'dark' && (! document.documentElement.classList.contains('dark'))) {
                        document.documentElement.classList.add('dark')
                    } else if (this.theme === 'light' && document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark')
                    }

                    $dispatch('dark-mode-toggled', this.theme)
                })
            },

            isSystemDark: function () {
                return window.matchMedia('(prefers-color-scheme: dark)').matches
            },
        }"
    >
        <div>
            @if (filament()->hasDarkMode())
                <x-filament::dropdown.list.item icon="heroicon-m-moon" x-show="theme === 'dark'" x-on:click="close(); mode = 'manual'; theme = 'light'">
                    {{ __('filament::layout.buttons.light_mode.label') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-sun" x-show="theme === 'light'" x-on:click="close(); mode = 'manual'; theme = 'dark'">
                    {{ __('filament::layout.buttons.dark_mode.label') }}
                </x-filament::dropdown.list.item>
            @endif
        </div>

        @foreach ($items as $key => $item)
            <x-filament::dropdown.list.item
                :color="$item->getColor() ?? 'gray'"
                :icon="$item->getIcon()"
                :href="$item->getUrl()"
                tag="a"
            >
                {{ $item->getLabel() }}
            </x-filament::dropdown.list.item>
        @endforeach

        <x-filament::dropdown.list.item
            :color="$logoutItem?->getColor() ?? 'gray'"
            :icon="$logoutItem?->getIcon() ?? 'heroicon-m-arrow-left-on-rectangle'"
            :action="$logoutItem?->getUrl() ?? filament()->getLogoutUrl()"
            method="post"
            tag="form"
        >
            {{ $logoutItem?->getLabel() ?? __('filament::layout.buttons.logout.label') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

{{ filament()->renderHook('user-menu.end') }}
