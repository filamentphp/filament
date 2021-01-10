<header role="banner"   
    tabindex="-1"
    id="banner"
    {{ $attributes->merge(['class' => 'bg-gray-900 text-gray-500 flex flex-col space-y-4 shadow-lg md:shadow-none']) }}>
    <x-filament::app-branding />
    <x-filament-nav class="flex-grow px-4 overflow-y-auto" />
    <x-filament::dropdown class="w-full text-left flex-grow flex items-center px-4 py-3 space-x-3 transition-colors duration-200 hover:text-white hover:bg-gray-800" id="dropdown-user">
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
</header>