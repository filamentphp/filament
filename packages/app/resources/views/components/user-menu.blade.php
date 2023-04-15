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

            systemTheme: null,

            matchesSystemTheme: null,

            init: function () {
                this.theme = localStorage.getItem('theme') || (this.isSystemDark() ? 'dark' : 'light')

                this.systemTheme = this.isSystemDark() ? 'dark' : 'light'

                if ( localStorage.getItem('mode') === 'manual' ) {
                    this.mode = 'manual'
                } else {
                    this.mode = 'auto'
                }

                this.matchesSystemTheme = this.isSystemTheme()

                console.log(this.mode)
                console.log('theme: ' + this.theme)
                console.log('system is dark: ' + this.isSystemDark())
                console.log('system matches: ' + this.isSystemTheme())
                    console.log('system check: ' + this.matchesSystemTheme)
                console.log('===')

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                    if (this.mode === 'auto') {
                        if (event.matches && (! document.documentElement.classList.contains('dark'))) {
                            this.theme = 'dark'

                            document.documentElement.classList.add('dark')
                        } else if ((! event.matches) && document.documentElement.classList.contains('dark')) {
                            this.theme = 'light'

                            document.documentElement.classList.remove('dark')
                        }
                    }

                    this.matchesSystemTheme = this.isSystemTheme()

                    console.log(this.mode)
                    console.log('theme: ' + this.theme)
                    console.log('system is dark: ' + this.isSystemDark())
                    console.log('system matches: ' + this.isSystemTheme())
                    console.log('system check: ' + this.matchesSystemTheme)
                    console.log('===')
                })

                $watch('mode', () => {
                    if (this.mode === 'auto') {
                        localStorage.setItem('mode', 'auto')
                        if (this.isSystemDark()) {
                            this.theme = 'dark'
                        } else {
                            this.theme = 'light'
                        }
                    } else {
                        localStorage.setItem('mode', 'manual')
                    }

                    this.matchesSystemTheme = this.isSystemTheme()

                    console.log(this.mode)
                    console.log('theme: ' + this.theme)
                    console.log('system is dark: ' + this.isSystemDark())
                    console.log('system matches: ' + this.isSystemTheme())
                    console.log('system check: ' + this.matchesSystemTheme)
                    console.log('===')
                })

                $watch('theme', () => {
                    localStorage.setItem('theme', this.theme)

                    if (this.theme === 'dark' && (! document.documentElement.classList.contains('dark'))) {
                        document.documentElement.classList.add('dark')
                    } else if (this.theme === 'light' && document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark')
                    }

                    this.matchesSystemTheme = this.isSystemTheme()

                    $dispatch('dark-mode-toggled', this.theme)

                    console.log(this.mode)
                    console.log('theme: ' + this.theme)
                    console.log('system is dark: ' + this.isSystemDark())
                    console.log('system matches: ' + this.isSystemTheme())
                    console.log('system check: ' + this.matchesSystemTheme)
                    console.log('===')
                })
            },

            isSystemDark: function () {
                return window.matchMedia('(prefers-color-scheme: dark)').matches
            },

            isSystemTheme: function () {
                if ( this.theme === 'dark' && this.isSystemDark() ) {
                    return true
                } else if ( this.theme === 'light' && !this.isSystemDark() ){
                    return true
                } else {
                    return false
                }
            },

            toggleTheme: function () {
                if (this.isSystemDark() && this.theme === 'dark') {
                    this.theme = 'light'
                    this.mode = 'manual'
                } else if (this.isSystemDark() && this.theme === 'light') {
                    this.theme = 'dark'
                    this.mode = 'auto'
                } else if (!this.isSystemDark() && this.theme === 'dark') {
                    this.theme = 'light'
                    this.mode = 'auto'
                } else if (!this.isSystemDark() && this.theme === 'light') {
                    this.theme = 'dark'
                    this.mode = 'manual'
                } else {
                    if (this.isSystemDark()) {
                        this.theme = 'dark'
                    } else {
                        this.theme = 'light'
                    }

                    this.mode = 'auto'
                }
            }
        }"
    >
        <div>
            @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
                <x-filament::dropdown.list.item icon="heroicon-m-moon" x-show="theme === 'dark'" x-on:click="close(); toggleTheme();">
                    {{ __('filament::layout.buttons.light_mode.label') }}
                    <p x-show="!matchesSystemTheme" class="font-light text-xs text-gray-300">Follow System Theme</p>
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-sun" x-show="theme === 'light'" x-on:click="close(); toggleTheme();">
                    {{ __('filament::layout.buttons.dark_mode.label') }}
                    <p x-show="!matchesSystemTheme" class="font-light text-xs text-gray-600">Follow System Theme</p>
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
