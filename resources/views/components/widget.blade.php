<x-filament::card tag="article">
    <div class="space-y-2">
        <div class="flex items-start justify-between space-x-4">
            <h2 class="font-medium leading-tight">{{ $title }}</h2>
            <x-filament::dropdown class="text-gray-400 hover:text-current transition-colors duration-200">
                <x-slot name="button">
                    <span class="sr-only">{{ __('filament::widgets.settings') }}</span>
                    <x-heroicon-o-cog class="w-5 h-5" aria-hidden="true" />
                </x-slot>

                <x-filament::dropdown-link button>
                    test
                </x-filament::dropdown-link>
            </x-filament::dropdown>
        </div>
        <div>
            {{ $slot }}
        </div>
    </div>
</x-filament::card>