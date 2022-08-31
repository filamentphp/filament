@php
    $user = \Filament\Facades\Filament::auth()->user();
@endphp

<x-filament::dropdown placement="bottom-end">
    <x-slot name="trigger">
        <x-filament::user-avatar :user="$user" />
    </x-slot>

    <ul
        class="py-1 space-y-1"
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
        @php
            $items = \Filament\Facades\Filament::getUserMenuItems();
            $accountItem = $items['account'] ?? null;
            $logoutItem = $items['logout'] ?? null;
        @endphp

        <x-filament::dropdown.item
            :color="$accountItem?->getColor() ?? 'secondary'"
            :icon="$accountItem?->getIcon() ?? 'heroicon-s-user-circle'"
            :href="$accountItem?->getUrl()"
            tag="a"
        >
            {{ $accountItem?->getLabel() ?? \Filament\Facades\Filament::getUserName($user) }}
        </x-filament::dropdown.item>

        <div>
            @if (config('filament.dark_mode'))
                <x-filament::dropdown.item icon="heroicon-s-moon" x-show="theme === 'dark'" x-on:click="close(); mode = 'manual'; theme = 'light'">
                    {{ __('filament::layout.buttons.light_mode.label') }}
                </x-filament::dropdown.item>

                <x-filament::dropdown.item icon="heroicon-s-sun" x-show="theme === 'light'" x-on:click="close(); mode = 'manual'; theme = 'dark'">
                    {{ __('filament::layout.buttons.dark_mode.label') }}
                </x-filament::dropdown.item>
            @endif
        </div>

        @foreach ($items as $key => $item)
            @if ($key !== 'account' && $key !== 'logout')
                <x-filament::dropdown.item
                    :color="$item->getColor() ?? 'secondary'"
                    :icon="$item->getIcon()"
                    :href="$item->getUrl()"
                    tag="a"
                >
                    {{ $item->getLabel() }}
                </x-filament::dropdown.item>
            @endif
        @endforeach

        <x-filament::dropdown.item
            :color="$logoutItem?->getColor() ?? 'secondary'"
            :icon="$logoutItem?->getIcon() ?? 'heroicon-s-logout'"
            :action="$logoutItem?->getUrl() ?? route('filament.auth.logout')"
            method="post"
            tag="form"
        >
            {{ $logoutItem?->getLabel() ?? __('filament::layout.buttons.logout.label') }}
        </x-filament::dropdown.item>
    </ul>
</x-filament::dropdown>
