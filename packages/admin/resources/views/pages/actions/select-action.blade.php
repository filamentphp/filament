<div class="filament-page-select-action">
    <label for="{{ $getId() }}" class="sr-only">
        {{ $getLabel() }}
    </label>

    <select
        id="{{ $getId() }}"
        wire:model="{{ $getName() }}"
        {{ $attributes->class([
            'text-gray-900 border-gray-300 invalid:text-gray-400 block w-full h-9 py-1 transition duration-75 rounded-lg shadow-sm outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
            'dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:border-primary-500' => config('filament.dark_mode'),
        ]) }}
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
