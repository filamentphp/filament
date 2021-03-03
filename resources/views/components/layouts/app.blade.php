<x-filament::layouts.base>
    <a href="#content" class="sr-only">Skip to content</a>

    <div class="relative overflow-hidden"
         role="group"
         tabindex="-1"
         x-data="{ headerIsOpen: false }"
         @keydown.escape.window="headerIsOpen = false"
         x-on:resize.window="if (window.outerWidth > 768) headerIsOpen = false"
    >
        <div class="fixed z-20 flex w-64 h-screen transition-transform duration-200 transform md:translate-x-0"
             :class="headerIsOpen ? 'translate-x-0' : '-translate-x-full'">
            <header role="banner"
                    tabindex="-1"
                    id="banner"
                    class="flex flex-col flex-grow space-y-4 overflow-y-auto text-gray-400 bg-gray-800 shadow-lg md:shadow-none">
                <x-filament::branding.app />

                <x-filament-nav class="flex-grow px-4 overflow-y-auto" />

                <x-filament::dropdown
                    class="flex items-center flex-grow w-full p-4 space-x-3 text-left transition-colors duration-200 hover:text-white hover:bg-gray-900">
                    <x-slot name="button">
                        <x-filament-avatar :user="Auth::guard('filament')->user()" :size="32" class="flex-shrink-0 w-8 h-8 rounded-full" />

                        <span class="flex-grow text-sm font-medium leading-tight">{{ Auth::guard('filament')->user()->name }}</span>
                    </x-slot>

                    @if (Auth::guard('filament')->user()->is_admin)
                        <x-filament::dropdown-link href="{{ route('filament.users.index') }}">
                            {{ __('filament::nav.dropdown.users.label') }}
                        </x-filament::dropdown-link>
                    @endif

                    <x-filament::dropdown-link href="{{ route('filament.account') }}">
                        {{ __('filament::nav.dropdown.account.label') }}
                    </x-filament::dropdown-link>

                    <livewire:filament.core.auth.logout class="w-full px-4 py-2 text-gray-600 transition-colors duration-200 hover:bg-gray-200" />
                </x-filament::dropdown>
            </header>

            <button
                type="button"
                aria-controls="banner"
                @click.prevent="headerIsOpen = false"
                :aria-expanded="headerIsOpen"
                x-cloak
                x-show.opacity="headerIsOpen"
                class="absolute right-0 p-3 text-gray-200 transition-colors duration-200 transform translate-x-full md:hidden top-2 hover:text-white">
                <x-heroicon-o-x class="w-6 h-6" />
            </button>
        </div>

        <span class="absolute inset-0 z-10 bg-gray-800 bg-opacity-50 md:hidden" x-cloak x-show="headerIsOpen"
              @click="headerIsOpen = false"></span>

        <div class="flex flex-col w-full min-h-screen md:pl-64">
            <div class="flex-grow">
                {{ $slot }}
            </div>

            <footer rel="contentinfo" class="p-4 text-center md:px-6">
                <x-filament::branding.footer />
            </footer>
        </div>
    </div>
</x-filament::layouts.base>
