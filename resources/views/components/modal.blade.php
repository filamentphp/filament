<div
    {{ $attributes }}
    x-data="{ modalIsOpen: false }" 
    x-init="
        $watch('modalIsOpen', value => {
            if (value === true) { 
                document.body.classList.add('overflow-hidden') 
            } else { 
                document.body.classList.remove('overflow-hidden') 
            }
        });
    "
    x-on:keydown.escape.window="modalIsOpen = false"
>
    <button
        type="button"
        @click="modalIsOpen = true"
    >
        {{ $slot }}
    </button>

    <div 
        x-show="modalIsOpen" 
        class="fixed z-40 inset-0 overflow-y-auto"
        :aria-hidden="!modalIsOpen"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"> 
            <div 
                x-show="modalIsOpen" 
                x-description="Background overlay, show/hide based on modal state." 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 transition-opacity" 
                aria-hidden="true"
            >
                <div class="absolute inset-0 bg-gray-800 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div 
                x-show="modalIsOpen"
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block align-bottom text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle" 
                role="dialog" 
                aria-modal="true" 
                aria-labelledby="modal-headline"
                @click.away="modalIsOpen = false"
            >
                <div class="flex flex-col space-y-4">
                    <button
                        type="button"
                        @click="modalIsOpen = false"
                        class="flex-shrink-0 self-center text-gray-200 hover:text-white transition-colors duration-200 flex"
                    >
                        <x-heroicon-o-x class="w-6 h-6" />
                    </button>
                    <div class="flex-grow">
                        {{ $content }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>