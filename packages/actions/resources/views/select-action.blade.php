@php
    $id = $getId();
@endphp

<div class="filament-actions-select-action">
    <label for="{{ $id }}" class="sr-only">
        {{ $getLabel() }}
    </label>

    <select
        id="{{ $id }}"
        wire:model="{{ $getName() }}"
        {{
            $attributes->class([
                'block w-full rounded-lg border-none bg-white py-1.5 pe-8 ps-3 text-base text-gray-950 shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-600 sm:text-sm sm:leading-6',
            ])
        }}
    >
        @if (($placeholder = $getPlaceholder()) !== null)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($getOptions() as $value => $label)
            <option value="{{ $value }}">
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>
