<div 
    x-data="{ open: {{ $notificationVisible ? 'true' : 'false' }} }" 
    x-show="open" 
    x-transition:enter="transition ease-out duration-100" 
    x-transition:enter-start="transform opacity-0 scale-95" 
    x-transition:enter-end="transform opacity-100 scale-100" 
    x-transition:leave="transition ease-in duration-75" 
    x-transition:leave-start="transform opacity-100 scale-100" 
    x-transition:leave-end="transform opacity-0 scale-95" 
    class="fixed top-0 right-0 mt-4 mr-4 shadow-lg rounded-md bg-gray-800 text-white p-4 flex items-start"
    role="alert">
    @if ($type)
        <div class="mr-2">
            @switch($type)
                @case('success')
                    {{ Filament::svg('heroicons/outline-md/md-check-circle', 'w-5 h-5 text-green-400') }}
                    @break

                @case('error')
                    {{ Filament::svg('heroicons/solid-sm/sm-exclamation-circle', 'h-5 w-5 text-red-500') }}
                    @break
            @endswitch
        </div>
    @endif
    <div class="flex-grow mr-4 text-sm">
        @markdown($message)
    </div>
    <button wire:click="close" class="text-gray-500 hover:text-gray-300 transition ease-in-out duration-200">
        {{ Filament::svg('heroicons/outline-md/md-x', 'w-5 h-5') }}
    </button>
</div>