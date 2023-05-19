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
    <x-slot name="trigger" class="ms-4">
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

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <div
            x-data="{
                theme: null,

                init: function () {
                    this.theme = localStorage.getItem('theme') || 'system'

                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                        if (this.mode !== 'system') return

                        if (event.matches && (! document.documentElement.classList.contains('dark'))) {
                            document.documentElement.classList.add('dark')
                        } else if ((! event.matches) && document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark')
                        }
                    })

                    $watch('theme', () => {
                        localStorage.setItem('theme', this.theme)

                        if (this.theme === 'dark' && (! document.documentElement.classList.contains('dark'))) {
                            document.documentElement.classList.add('dark')
                        } else if (this.theme === 'light' && document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark')
                        } else if (this.theme === 'system') {
                            if (this.isSystemDark() && (! document.documentElement.classList.contains('dark'))) {
                                document.documentElement.classList.add('dark')
                            } else if ((! this.isSystemDark()) && document.documentElement.classList.contains('dark')) {
                                document.documentElement.classList.remove('dark')
                            }
                        }

                        $dispatch('dark-mode-toggled', this.theme)
                    })
                },

                isSystemDark: function () {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches
                }
            }"
            class="filament-mode-toggle flex items-center border-b border-gray-950/5 divide-x divide-gray-950/5 dark:border-gray-700 dark:divide-gray-700"
        >
            <button
                type="button"
                class="flex-1 p-2 hover:bg-gray-500/10 focus:bg-gray-500/10"
                aria-label="{{ __('filament::layout.buttons.light_mode.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.light_mode.label') }}'"
                x-bind:class="theme === 'light' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                x-on:click="close(); theme = 'light'"
            >
                @svg('heroicon-m-sun', 'h-5 w-5 mx-auto')
            </button>
            <button
                type="button"
                class="flex-1 p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
                aria-label="{{ __('filament::layout.buttons.dark_mode.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.dark_mode.label') }}'"
                x-bind:class="theme === 'dark' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                x-on:click="close(); theme = 'dark'"
            >
                @svg('heroicon-m-moon', 'h-5 w-5 mx-auto')
            </button>
            <button
                type="button"
                class="flex-1 p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
                aria-label="{{ __('filament::layout.buttons.system_mode.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.system_mode.label') }}'"
                x-bind:class="theme === 'system' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                x-on:click="close(); theme = 'system'"
            >
                @svg('heroicon-m-computer-desktop', 'h-5 w-5 mx-auto')
            </button>
        </div>
    @endif

    <x-filament::dropdown.list>
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
