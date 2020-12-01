<header role="banner" 
    class="bg-gray-900 text-gray-500 w-56 flex flex-col space-y-4 shadow-lg md:shadow-none absolute z-10 inset-y-0 md:static transform transition-transform duration-200 md:translate-x-0" 
    :class="headerIsOpen ? 'translate-x-0' : '-translate-x-full'" 
    tabindex="-1"
    id="banner">
    <x-filament::app-branding />
    <x-filament-nav />
    <x-filament::dropdown class="w-full text-left flex-grow flex items-center p-4 space-x-3 transition-colors duration-200 hover:text-white hover:bg-gray-800" id="dropdown-user">
        <x-filament-avatar :user="Auth::user()" :size="32" class="flex-shrink-0 w-8 h-8 rounded-full" />
        <span class="flex-grow text-sm leading-tight font-semibold">{{ Auth::user()->name }}</span>

        <x-slot name="content">
            <ul class="list-dropdown" aria-label="{{ __('User actions') }}">
                @if (Filament\Features::hasUserProfile())
                    <li><a href="{{ route('filament.profile') }}">{{ __('Edit Profile') }}</a></li>
                @endif
                <li><livewire:filament-logout /></li>
            </ul>
        </x-slot>
    </x-filament::dropdown>
    <button type="button" aria-controls="banner" @click.prevent="headerIsOpen = false" :aria-expanded="headerIsOpen" x-cloak x-show.opacity="headerIsOpen" class="md:hidden absolute top-0 right-0 transform translate-x-full -translate-y-2 p-3 text-gray-200 hover:text-white transition-colors duration-200">
        <x-heroicon-o-x class="w-6 h-6" />
    </button>
</header>
<span class="absolute z-0 inset-0 bg-black bg-opacity-50 md:hidden flex items-start justify-end" x-cloak x-show="headerIsOpen" @click="headerIsOpen = false"></span>
