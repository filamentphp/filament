@php
    $user = \Filament\Facades\Filament::auth()->user();
@endphp

<div
    x-data="{
        isOpen: false,

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
    {{ $attributes->class(['relative']) }}
>
    <button
        x-on:click="isOpen = ! isOpen"
        @class([
            'block flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center',
            'dark:bg-gray-900' => config('filament.dark_mode'),
        ])
        style="background-image: url('{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}')"
    ></button>

    <div
        x-show="isOpen"
        x-on:click.away="isOpen = false"
        x-transition:enter="transition"
        x-transition:enter-start="-translate-y-1 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-1 opacity-0"
        x-cloak
        class="absolute z-10 right-0 rtl:right-auto rtl:left-0 mt-2 shadow-xl rounded-xl w-52 top-full"
    >
        <ul @class([
            'py-1 space-y-1 overflow-hidden bg-white shadow rounded-xl',
            'dark:border-gray-600 dark:bg-gray-700' => config('filament.dark_mode'),
        ])>
            <li class="flex items-center w-full h-8 px-3 text-sm font-medium">
                <x-heroicon-s-user-circle class="mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 w-6 h-6 text-gray-500" />

                {{ \Filament\Facades\Filament::getUserName($user) }}
            </li>

            <div>
                @if (config('filament.dark_mode'))
                    <x-filament::dropdown.item icon="heroicon-s-moon" x-show="theme === 'dark'" x-on:click="mode = 'manual'; theme = 'light'">
                        {{ __('filament::layout.buttons.light_mode.label') }}
                    </x-filament::dropdown.item>

                    <x-filament::dropdown.item icon="heroicon-s-sun" x-show="theme === 'light'" x-on:click="mode = 'manual'; theme = 'dark'">
                        {{ __('filament::layout.buttons.dark_mode.label') }}
                    </x-filament::dropdown.item>
                @endif
            </div>

            <x-filament::dropdown.item icon="heroicon-s-logout" color="secondary" :href="route('filament.auth.logout')" tag="a">
                {{ __('filament::layout.buttons.logout.label') }}
            </x-filament::dropdown.item>
        </ul>
    </div>
</div>
