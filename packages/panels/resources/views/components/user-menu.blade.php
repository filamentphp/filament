@php
    $user = filament()->auth()->user();
    $items = filament()->getUserMenuItems();

    $profileItem = $items['profile'] ?? $items['account'] ?? null;
    $profileItemUrl = $profileItem?->getUrl();
    $profilePage = filament()->getProfilePage();
    $hasProfile = filament()->hasProfile() || filled($profileItemUrl);

    $logoutItem = $items['logout'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['account', 'logout', 'profile']);
@endphp

{{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.start') }}

<x-filament::dropdown placement="bottom-end" class="fi-user-menu">
    <x-slot name="trigger" class="ms-4">
        <button
            class="block"
            aria-label="{{ __('filament::layout.actions.open_user_menu.label') }}"
        >
            <x-filament::avatar.user :user="$user" />
        </button>
    </x-slot>

    {{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.profile.before') }}

    @if (filled($hasProfile))
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                :color="$profileItem?->getColor() ?? 'gray'"
                :icon="$profileItem?->getIcon() ?? 'heroicon-m-user-circle'"
                :href="$profileItemUrl ?? filament()->getProfileUrl()"
                tag="a"
            >
                {{ $profileItem?->getLabel() ?? ($profilePage ? $profilePage::getLabel() : null) ?? filament()->getUserName($user) }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    @else
        <x-filament::dropdown.header
            :color="$profileItem?->getColor() ?? 'gray'"
            :icon="$profileItem?->getIcon() ?? 'heroicon-m-user-circle'"
        >
            {{ $profileItem?->getLabel() ?? filament()->getUserName($user) }}
        </x-filament::dropdown.header>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.profile.after') }}

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <div
            x-data="{
                theme: null,

                init: function () {
                    this.theme = localStorage.getItem('theme') || 'system'

                    window
                        .matchMedia('(prefers-color-scheme: dark)')
                        .addEventListener('change', (event) => {
                            if (this.theme !== 'system') {
                                return
                            }

                            if (
                                event.matches &&
                                ! document.documentElement.classList.contains('dark')
                            ) {
                                document.documentElement.classList.add('dark')
                            } else if (
                                ! event.matches &&
                                document.documentElement.classList.contains('dark')
                            ) {
                                document.documentElement.classList.remove('dark')
                            }
                        })

                    $watch('theme', () => {
                        localStorage.setItem('theme', this.theme)

                        if (
                            this.theme === 'dark' &&
                            ! document.documentElement.classList.contains('dark')
                        ) {
                            document.documentElement.classList.add('dark')
                        } else if (
                            this.theme === 'light' &&
                            document.documentElement.classList.contains('dark')
                        ) {
                            document.documentElement.classList.remove('dark')
                        } else if (this.theme === 'system') {
                            if (
                                this.isSystemDark() &&
                                ! document.documentElement.classList.contains('dark')
                            ) {
                                document.documentElement.classList.add('dark')
                            } else if (
                                ! this.isSystemDark() &&
                                document.documentElement.classList.contains('dark')
                            ) {
                                document.documentElement.classList.remove('dark')
                            }
                        }

                        Alpine.store('theme', this.theme)
                    })
                },

                isSystemDark: function () {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches
                },
            }"
            class="fi-theme-switcher flex items-center divide-x divide-gray-100 dark:divide-gray-800"
        >
            <button
                type="button"
                aria-label="{{ __('filament::layout.actions.theme_switcher.light.label') }}"
                x-tooltip="'{{ __('filament::layout.actions.theme_switcher.light.label') }}'"
                x-on:click="theme = 'light'"
                x-bind:class="theme === 'light' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-sun"
                    alias="panels::topbar.user-menu.theme-switcher.light-button"
                    class="h-5 w-5"
                />
            </button>

            <button
                type="button"
                aria-label="{{ __('filament::layout.actions.theme_switcher.dark.label') }}"
                x-tooltip="'{{ __('filament::layout.actions.theme_switcher.dark.label') }}'"
                x-on:click="theme = 'dark'"
                x-bind:class="theme === 'dark' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-moon"
                    alias="panels::topbar.user-menu.theme-switcher.dark-button"
                    class="h-5 w-5"
                />
            </button>

            <button
                type="button"
                aria-label="{{ __('filament::layout.actions.theme_switcher.system.label') }}"
                x-tooltip="'{{ __('filament::layout.actions.theme_switcher.system.label') }}'"
                x-on:click="theme = 'system'"
                x-bind:class="theme === 'system' ? 'text-primary-500' : 'text-gray-700 dark:text-gray-200'"
                class="flex flex-1 items-center justify-center p-2 text-gray-700 hover:bg-gray-500/10 focus:bg-gray-500/10"
            >
                <x-filament::icon
                    name="heroicon-m-computer-desktop"
                    alias="panels::topbar.user-menu.theme-switcher.system-button"
                    class="h-5 w-5"
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
            {{ $logoutItem?->getLabel() ?? __('filament::layout.actions.logout.label') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>

{{ \Filament\Support\Facades\FilamentView::renderHook('user-menu.end') }}
