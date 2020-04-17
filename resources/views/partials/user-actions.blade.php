<x-filament-dropdown 
    class="flex-grow" 
    button-class="w-full text-left flex items-center"
    dropdown-class="origin-bottom-left left-0 bottom-0 w-56 mb-12"
>
    <x-slot name="button">
        <div class="flex-shrink-0">
            @livewire('filament::user-avatar', [
                'userId' => auth()->user()->id, 
                'size' => 36, 
                'classes' => 'h-9 w-9 rounded-full',
            ])
        </div>
        @livewire('filament::auth-user-attribute', [
            'attribute' => 'name', 
            'classes' => 'flex-grow ml-3 text-sm leading-5 font-medium text-gray-50',
        ])
    </x-slot>
    <p class="px-4 py-3">
        <span class="block text-xs leading-5 text-gray-400">
            {{ __('Signed in as') }}
        </span>
        @livewire('filament::auth-user-attribute', [
            'attribute' => 'email', 
            'classes' => 'font-medium',
        ])
    </p>
    <a 
        href="{{ route('filament.admin.users.edit', ['id' => auth()->user()->id]) }}" 
    >
        {{ __('Account settings') }}
    </a>
    <x-filament-form :action="route('filament.auth.logout')">
        <button type="submit">
            {{ __('Sign Out') }}
        </button>
    </x-filament-form>
</x-filament-dropdown>
