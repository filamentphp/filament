@props([
    'actions' => null,
    'ariaLabelledby' => null,
    'closeEventName' => 'close-modal',
    'darkMode' => false,
    'displayClasses' => 'inline-block',
    'footer' => null,
    'header' => null,
    'heading' => null,
    'headingComponent' => 'filament-support::modal.heading',
    'hrComponent' => 'filament-support::hr',
    'id' => null,
    'openEventName' => 'open-modal',
    'subheading' => null,
    'subheadingComponent' => 'filament-support::modal.subheading',
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

<div
    x-data="{ isOpen: false }"
    x-trap.noscroll="isOpen"
    @if ($id)
        x-on:{{ $closeEventName }}.window="if ($event.detail.id === '{{ $id }}') isOpen = false"
        x-on:{{ $openEventName }}.window="if ($event.detail.id === '{{ $id }}') isOpen = true"
    @endif
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    role="dialog"
    aria-modal="true"
    class="filament-modal {{ $displayClasses }}"
>
    {{ $trigger }}

    <div
        x-show="isOpen"
        x-transition:enter="ease duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-40 flex items-center min-h-screen p-4 overflow-y-auto transition"
    >
        <div
            @if (config('filament-support.modal.is_closed_by_clicking_away', true))
                @if (filled($id))
                    x-on:click="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
                @else
                    x-on:click="isOpen = false"
                @endif
            @endif
            aria-hidden="true"
            @class([
                'filament-modal-close-overlay fixed inset-0 w-full h-full bg-black/50',
                'cursor-pointer' => config('filament-support.modal.is_closed_by_clicking_away', true)
            ])
        ></div>

        <div
            x-show="isOpen"
            @if (filled($id))
                x-on:keydown.window.escape="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
            @else
                x-on:keydown.window.escape="isOpen = false"
            @endif
            x-transition:enter="ease duration-300"
            x-transition:enter-start="translate-y-8"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease duration-300"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-8"
            x-cloak
            {{ $attributes->class(['relative w-full my-auto cursor-pointer pointer-events-none']) }}
        >
            <div
                @class([
                    'filament-modal-window w-full mx-auto p-2 space-y-2 bg-white rounded-xl cursor-default pointer-events-auto',
                    'dark:bg-gray-800' => $darkMode,
                    'hidden' => ! $visible,
                    'max-w-xs' => $width === 'xs',
                    'max-w-sm' => $width === 'sm',
                    'max-w-md' => $width === 'md',
                    'max-w-lg' => $width === 'lg',
                    'max-w-xl' => $width === 'xl',
                    'max-w-2xl' => $width === '2xl',
                    'max-w-3xl' => $width === '3xl',
                    'max-w-4xl' => $width === '4xl',
                    'max-w-5xl' => $width === '5xl',
                    'max-w-6xl' => $width === '6xl',
                    'max-w-7xl' => $width === '7xl',
                ])
            >
                @if ($header)
                    <div class="filament-modal-header px-4 py-2">
                        {{ $header }}
                    </div>
                @endif

                @if ($header && ($actions || $heading || $slot->isNotEmpty() || $subheading))
                    <x-dynamic-component :component="$hrComponent" />
                @endif

                <div class="filament-modal-content space-y-2">
                    @if ($heading || $subheading)
                        <div @class([
                            'p-4 space-y-2 text-center',
                            'dark:text-white' => $darkMode,
                        ])>
                            @if ($heading)
                                <x-dynamic-component
                                    :component="$headingComponent"
                                    :id="$id . '.heading'"
                                >
                                    {{ $heading }}
                                </x-dynamic-component>
                            @endif

                            @if ($subheading)
                                <x-dynamic-component :component="$subheadingComponent">
                                    {{ $subheading }}
                                </x-dynamic-component>
                            @endif
                        </div>
                    @endif

                    @if ($slot->isNotEmpty())
                        <div class="px-4 py-2 space-y-4">
                            {{ $slot }}
                        </div>
                    @endif

                    {{ $actions }}
                </div>

                @if ($footer && ($actions || $heading || $slot->isNotEmpty() || $subheading))
                    <x-dynamic-component :component="$hrComponent" />
                @endif

                @if ($footer)
                    <div class="filament-modal-footer px-4 py-2">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
