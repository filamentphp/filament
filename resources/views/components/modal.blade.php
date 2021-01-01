<div
    {{ $attributes }}
    x-data="{ open: false }" 
    x-init="
        $watch('open', value => {
            if (value === true) { 
                document.body.classList.add('overflow-hidden') 
            } else { 
                document.body.classList.remove('overflow-hidden') 
            }
        });
    "
>
    <button
        type="button"
        @click="open = true"
    >
        {{ $button }}
    </button>
    <div 
        x-show="open" 
        class="fixed z-40 inset-0 flex"
        :aria-hidden="!open"
    >
        <div 
            x-show="open" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            class="fixed inset-0 transition-opacity absolute inset-0 bg-gray-800 bg-opacity-75" 
            aria-hidden="true"
        ></div>
        <div class="flex-grow flex items-center justify-center p-4 overflow-y-auto">
            <div 
                x-show="open" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative transform transition-all" 
                role="dialog" 
                aria-modal="true" 
                @click.away="open = false"
            >
                <div class="flex flex-col space-y-4">
                    <button
                        type="button" 
                        @click="open = false"
                        class="flex-shrink-0 self-center text-gray-200 hover:text-white transition-colors duration-200 flex"
                    >
                        <x-heroicon-o-x class="w-6 h-6" />
                        <span class="sr-only">{{ __('Close') }}</span>
                    </button>
                    <div class="flex-grow">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>