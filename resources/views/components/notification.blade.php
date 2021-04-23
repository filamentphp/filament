<div
    class="fixed z-50 inset-0 flex items-end justify-center p-4 md:px-8 pointer-events-none sm:items-start sm:justify-end">
    <div
        x-data="{ show: false, message: '' }"
        x-cloak
        x-on:notify.window="show = true; message = event.detail; setTimeout(() => { show = false }, 2500)"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="max-w-sm w-full bg-gray-700 shadow-lg rounded pointer-events-auto overflow-hidden"
        role="status"
        aria-live="polite"
    >
        <div class="p-4">
            <div class="flex items-start space-x-4 rtl:space-x-reverse">
                <div class="flex-grow flex items-center space-x-2 rtl:space-x-reverse">
                    <x-heroicon-o-information-circle class="flex-shrink-0 w-5 h-5 text-blue-500 stroke-current" />

                    <div class="flex-grow">
                        <p x-text="message" class="text-sm text-white"></p>
                    </div>
                </div>

                <div class="flex-shrink-0 flex">
                    <button @click="show = false"
                            class="text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none">
                        <span class="sr-only">Close</span>

                        <x-heroicon-o-x class="w-5 h-5 fill-current" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
