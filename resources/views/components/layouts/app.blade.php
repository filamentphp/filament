<x-filament::layouts.base>
    <a href="#content" class="sr-only">Skip to content</a>

    <div class="relative overflow-hidden"
         role="group"
         tabindex="-1"
         x-data="{ headerIsOpen: false }"
         @keydown.escape.window="headerIsOpen = false"
         x-on:resize.window="if (window.outerWidth > 768) headerIsOpen = false"
    >
        <div class="w-56 fixed z-20 h-screen transform transition-transform duration-200 md:translate-x-0 flex"
             :class="headerIsOpen ? 'translate-x-0' : '-translate-x-full'">
            <header role="banner"
                    tabindex="-1"
                    id="banner"
                    class="flex-grow overflow-y-auto bg-gray-900 text-gray-500 flex flex-col space-y-4 shadow-lg md:shadow-none">
                <a
                    href="{{ url('/') }}"
                    rel="home"
                    class="p-4 flex items-center space-x-3 group"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <div class="flex-shrink-0 rounded w-8 h-8 bg-red-800 text-white flex items-center justify-center transition-colors duration-200 group-hover:bg-red-700">
                        <span class="font-bold italic">{{ substr(config('app.name'), 0, 1) }}</span>
                    </div>

                    <span class="text-sm leading-tight font-bold transition-colors duration-200 group-hover:text-white">{{ config('app.name') }}</span>
                </a>

                <x-filament-nav class="flex-grow px-4 overflow-y-auto" />
                <x-filament::dropdown
                    class="w-full text-left flex-grow flex items-center px-4 py-3 space-x-3 transition-colors duration-200 hover:text-white hover:bg-gray-800">
                    <x-slot name="button">
                        <x-filament-avatar :user="Auth::user()" :size="32" class="flex-shrink-0 w-8 h-8 rounded-full" />
                        <span class="flex-grow text-sm leading-tight font-medium">{{ Auth::user()->name }}</span>
                    </x-slot>

                    <x-filament::dropdown-link
                        href="{{ route('filament.account') }}">{{ __('filament::edit-account.edit') }}</x-filament::dropdown-link>
                    <livewire:filament.auth.logout
                        class="w-full py-2 px-4 transition-colors duration-200 text-gray-600 hover:bg-gray-200" />
                </x-filament::dropdown>
            </header>

            <button
                type="button"
                aria-controls="banner"
                @click.prevent="headerIsOpen = false"
                :aria-expanded="headerIsOpen"
                x-cloak
                x-show.opacity="headerIsOpen"
                class="md:hidden absolute top-2 right-0 transform translate-x-full p-3 text-gray-200 hover:text-white transition-colors duration-200">
                <x-heroicon-o-x class="w-6 h-6" />
            </button>
        </div>

        <span class="absolute z-10 inset-0 bg-gray-800 bg-opacity-50 md:hidden" x-cloak x-show="headerIsOpen"
              @click="headerIsOpen = false"></span>

        <div class="min-h-screen w-full md:pl-56 flex flex-col">
            {{ $slot }}

            <footer rel="contentinfo" class="p-4 md:px-6 text-center">
                <x-filament::branding-footer />
            </footer>
        </div>
    </div>
</x-filament::layouts.base>
