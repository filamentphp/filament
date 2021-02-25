@props([
    'selected' => [],
])

<span
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
    x-on:keydown.escape.window="open = false"
    x-cloak
>
    <button
        x-on:click="open = true"
        {!! count($selected) === 0 ? 'disabled' : null !!}
        type="button"
        class="{{ count($selected) === 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }} font-medium border rounded transition duration-200 shadow-sm focus:ring focus:ring-opacity-50 text-sm py-2 px-4 border-transparent bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-200"
    >
        {{ __('tables::table.delete.button.label') }}
    </button>

    <div
        x-show="open"
        x-bind:aria-hidden="! open"
        class="fixed z-40 inset-0 overflow-y-auto"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center cursor-default sm:block sm:p-0">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                aria-hidden="true"
                class="fixed inset-0 transition-opacity"
            >
                <div class="absolute inset-0 bg-gray-800 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                role="dialog"
                aria-modal="true"
                x-on:click.away="open = false"
                class="inline-block align-bottom text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle"
            >
                <div
                    class="flex flex-col space-y-4"
                >
                    <div class="flex-grow">
                        <div class="bg-white shadow-xl rounded p-4 md:p-6 space-y-5 max-w-2xl">
                            <div class="space-y-2">
                                <h3 class="text-lg leading-tight font-medium">
                                    {{ __('tables::table.delete.modal.heading') }}
                                </h3>

                                <p class="text-sm text-gray-500">
                                    {{ __('tables::table.delete.modal.description') }}
                                </p>
                            </div>

                            <div class="space-y-3 sm:space-y-0 sm:flex sm:space-x-3 sm:justify-end">
                                <button
                                    x-on:click="open = false"
                                    type="button"
                                    class="cursor-pointer font-medium border rounded transition duration-200 shadow-sm focus:ring focus:ring-opacity-50 text-sm py-2 px-4 border-gray-300 bg-white text-gray-800 hover:bg-gray-100 focus:ring-primary-200"
                                >
                                    {{ __('tables::table.delete.modal.buttons.cancel.label') }}
                                </button>

                                <button
                                    wire:click="deleteSelected"
                                    x-on:click="open = false"
                                    type="button"
                                    class="cursor-pointer font-medium border rounded transition duration-200 shadow-sm focus:ring focus:ring-opacity-50 text-sm py-2 px-4 border-transparent bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-200"
                                >
                                    {{ __('tables::table.delete.modal.buttons.delete.label') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</span>
