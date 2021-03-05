@props([
    'closeButton' => false,
    'name' => null,
])

<span
    {{ $attributes->except('class') }}
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
    x-on:open.window="if ('{{ $name }}' && $event.detail === '{{ (string) Str::of($name)->replace('\\', '\\\\') }}') open = true"
    x-on:close.window="if ('{{ $name }}' && $event.detail === '{{ (string) Str::of($name)->replace('\\', '\\\\') }}') open = false"
    x-cloak
>
    {{ $trigger ?? null }}

    <div
        x-show="open"
        x-bind:aria-hidden="! open"
        class="fixed inset-0 z-40 overflow-y-auto"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center cursor-default sm:block sm:p-0">
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
                {{ $attributes->only('class')->merge(['class' => 'inline-block text-left align-bottom transition-all transform sm:my-8 sm:align-middle']) }}
            >
                <div
                    class="flex flex-col space-y-4"
                >
                    @if ($closeButton)
                        <button
                            type="button"
                            x-on:click="open = false"
                            class="flex self-center flex-shrink-0 text-gray-200 transition-colors duration-200 hover:text-white"
                        >
                            <x-heroicon-o-x class="w-6 h-6" />
                        </button>
                    @endif

                    <div class="flex-grow">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</span>
