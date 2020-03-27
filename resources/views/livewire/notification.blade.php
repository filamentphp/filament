<div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
    <div 
        x-data="{ open: {{ $notificationVisible ? 'true' : 'false' }} }" 
        x-show="open" 
        x-transition:enter="transform ease-out duration-300 transition" 
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        class="max-w-sm w-full pointer-events-auto shadow-lg rounded bg-gray-800 dark:bg-black text-gray-50 p-4 flex items-start"
        role="alert"
    >
        <div class="flex-shrink-0">
            @switch($type)
                @case('success')
                    {{ Filament::svg('heroicons/outline-md/md-check-circle', 'w-5 h-5 text-green-400') }}
                    @break
                @case('error')
                    {{ Filament::svg('heroicons/solid-sm/sm-exclamation-circle', 'h-5 w-5 text-red-400') }}
                    @break
                @default
                    {{ Filament::svg('heroicons/outline-md/md-information-circle', 'w-5 h-5 text-green-400') }}
            @endswitch
        </div>
        <div class="ml-3 flex-grow mr-4 text-sm leading-5">
            @markdown($message)
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button wire:click="close" class="text-gray-500 hover:text-gray-300 transition ease-in-out duration-200">
                {{ Filament::svg('heroicons/outline-md/md-x', 'w-5 h-5') }}
            </button>
        </div>
    </div>
</div>