@props([
    'color' => 'primary',
    'darkMode' => false,
    'disabled' => false,
    'icon' => null,
    'keyBindings' => null,
    'label' => null,
    'tag' => 'button',
    'tooltip' => null,
    'type' => 'button',
])

@php
    $buttonClasses = [
        'flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-500/5 focus:outline-none filament-icon-button',
        'text-primary-500 focus:bg-primary-500/10' => $color === 'primary',
        'text-danger-500 focus:bg-danger-500/10' => $color === 'danger',
        'text-gray-500 focus:bg-gray-500/10' => $color === 'secondary',
        'text-success-500 focus:bg-success-500/10' => $color === 'success',
        'text-warning-500 focus:bg-warning-500/10' => $color === 'warning',
        'hover:bg-gray-300/5' => $darkMode,
        'opacity-70 cursor-not-allowed' => $disabled,
    ];

    $iconClasses = 'w-5 h-5 filament-icon-button-icon';
@endphp

@if ($tag === 'button')
    <button
        @if ($keyBindings)
            x-mousetrap.global.{{ implode('.', $keyBindings) }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        type="{{ $type }}"
        {!! $disabled ? 'disabled' : '' !!}
        @if ($form)
            x-data="{
                form: null,
                label: {{ \Illuminate\Support\Js::from($slot->toHtml()) }},
                isUploadingFile: false
            }"
            x-html="isUploadingFile ? '{{ __('filament-support::components/icon-button.messages.uploading_file') }}' : label"
            x-bind:disabled="isUploadingFile"
            x-bind:class="{ 'opacity-70 cursor-wait': isUploadingFile }"
            x-init="
                form = $el.closest('form')

                form?.addEventListener('file-upload-started', () => {
                    isUploadingFile = true
                })

                form?.addEventListener('file-upload-finished', () => {
                    isUploadingFile = false
                })
            "
        @elseif ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-dynamic-component :component="$icon" :class="$iconClasses" />
    </button>
@elseif ($tag === 'a')
    <a
        @if ($keyBindings || $tooltip)
            x-data="{}"
        @endif
        @if ($keyBindings)
            x-mousetrap.global.{{ implode('.', $keyBindings) }}
        @endif
        @if ($tooltip)
            x-tooltip.raw="{{ $tooltip }}"
        @endif
        {{ $attributes->class($buttonClasses) }}
    >
        @if ($label)
            <span class="sr-only">
                {{ $label }}
            </span>
        @endif

        <x-dynamic-component :component="$icon" :class="$iconClasses" />
    </a>
@endif
