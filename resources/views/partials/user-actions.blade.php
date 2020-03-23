<div @click.away="open = false" class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center">
        @livewire('filament::user-avatar', [
            'userId' => auth()->user()->id, 
            'size' => 36, 
            'classes' => 'h-9 w-9 rounded-full',
        ])
        @livewire('filament::auth-user-attribute', [
            'attribute' => 'name', 
            'classes' => 'ml-3 text-sm leading-5 font-medium text-white',
        ])
    </button>
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95" 
        class="origin-bottom-left absolute left-0 bottom-0 mb-12 w-56 rounded shadow-lg">
        <div class="rounded bg-white shadow-xs">
            <p class="px-4 py-3">
                <span class="block text-sm leading-5">
                    {{ __('filament::auth.signed_in_as') }}
                </span>
                @livewire('filament::auth-user-attribute', [
                    'attribute' => 'email', 
                    'classes' => 'text-sm leading-5 font-medium text-gray-900',
                ])
            </p>
            <div class="border-t border-gray-100"></div>
            <div class="py-1">
                <a href="{{ route('filament.admin.users.edit', ['user' => auth()->user()->id]) }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">{{ __('filament::user.settings') }}</a>
            </div>
            <div class="border-t border-gray-100"></div>
            <div class="py-1">
                <x-filament-form :action="route('filament.auth.logout')">
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                        {{ __('Sign Out') }}
                    </button>
                </x-filament-form>
            </div>
        </div>
    </div>
</div>