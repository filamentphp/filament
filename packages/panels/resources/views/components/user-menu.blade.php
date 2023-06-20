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
        <button
            class="block"
            aria-label="{{ __('filament::layout.buttons.user_menu.label') }}"
        >
            <x-filament::avatar.user :user="$user" />
        </button>
    </x-slot>

    {{ filament()->renderHook('user-menu.account.before') }}

    @if (filled($accountItemUrl))
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="$accountItem->getColor() ?? 'gray'"
                :icon="$accountItem->getIcon() ?? 'heroicon-m-user-circle'"
                :href="$accountItemUrl"
                tag="a"
            >
                {{ $accountItem->getLabel() ?? filament()->getUserName($user) }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    @else
        <x-filament::dropdown.header
            :color="$accountItem?->getColor() ?? 'gray'"
            :icon="$accountItem?->getIcon() ?? 'heroicon-m-user-circle'"
        >
            {{ $accountItem?->getLabel() ?? filament()->getUserName($user) }}
        </x-filament::dropdown.header>
    @endif

    {{ filament()->renderHook('user-menu.account.after') }}

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <div
            x-data="{
                theme: null,

                init: function () {
                    this.theme = localStorage.getItem('theme') || 'system'

                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                        if (this.theme !== 'system') {
                            return
                        }

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

                        $dispatch('theme-changed', this.theme)
                    })
                },

                isSystemDark: function () {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches
                },
            }"
            class="filament-theme-switcher flex items-center divide-x divide-gray-950/5 border-b border-gray-950/5 dark:divide-gray-700 dark:border-gray-700"
        >
            <button
                type="button"
                aria-label="{{ __('filament::layout.buttons.light_theme.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.light_theme.label') }}'"
                x-on:click="theme = 'light'"
                x-bind:class="theme === 'light' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-sun"
                    alias="panels::theme.light"
                    size="h-5 w-5"
                />
            </button>

            <button
                type="button"
                aria-label="{{ __('filament::layout.buttons.dark_theme.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.dark_theme.label') }}'"
                x-on:click="theme = 'dark'"
                x-bind:class="theme === 'dark' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-moon"
                    alias="panels::theme.dark"
                    size="h-5 w-5"
                />
            </button>

            <button
                type="button"
                aria-label="{{ __('filament::layout.buttons.system_theme.label') }}"
                x-tooltip="'{{ __('filament::layout.buttons.system_theme.label') }}'"
                x-on:click="theme = 'system'"
                x-bind:class="theme === 'system' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-computer-desktop"
                    alias="panels::theme.system"
                    size="h-5 w-5"
                />
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
