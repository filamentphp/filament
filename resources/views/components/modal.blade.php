<div
    x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }" 
    x-show="open" 
    class="fixed bottom-0 inset-x-0 px-4 pb-6 sm:inset-0 sm:p-0 sm:flex sm:items-center sm:justify-center"
    @filament-toggle-modal.window="if ($event.detail.id === '{{ $id }}') { 
        $event.stopPropagation();
        open = !open; 
    }"
    @if ($escClose)
        @keydown.escape.window="open = false"
    @endif
>
    <div   
        x-show="open" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 transition-opacity"
    >
        <div class="absolute inset-0 bg-black opacity-50"></div>
    </div>
    
    <div 
        @if ($clickOutside)
            @click.away="open = false"
        @endif
        x-show="open" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
        x-transition:leave="ease-in duration-200" 
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
        role="dialog"
        aria-label="{{ $label }}"
        aria-modal="true"
        x-bind:aria-hidden="open === false"
        {{ $attributes->merge(['class' => 'relative sm:w-full']) }}
    >
        <div class="bg-gray-100 dark:bg-gray-800 rounded px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:p-6">
            {{ $slot }}
        </div>
        <button 
            type="button"
            @click.prevent="open = false"  
            class="absolute top-0 right-3 -mt-10 flex transition ease-in-out duration-150 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark-hover:text-gray-300 focus:outline-none"
        >
            <x-heroicon-o-x class="h-6 w-6 text-gray-50" aria-hidden="true" />
            <span class="sr-only">{{ __('Close') }}</span>
        </button>
    </div>
</div>

@pushonce('scripts')
    <script>
        window.livewire.on('filament.toggleModal', modalId => {
            window.dispatchEvent(new CustomEvent('filament-toggle-modal', { 
                bubbles: true,
                detail: { id: modalId.toString() }
            }));
        })
    </script>
@endpushonce