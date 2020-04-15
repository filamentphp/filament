<div {{ $attributes->merge(['class' => 'btn inline-flex p-0']) }}>
    <button type="{{ $type }}" class="px-5 py-2 rounded-l transition duration-150 ease-in-out hover:bg-indigo-800">{{ $label }}</button>
    <x-filament-dropdown button-class="p-2 bg-indigo-600 rounded-r transition duration-150 ease-in-out">
        <x-slot name="button">
            <span class="sr-only">{{ __('Options') }}</span>
            <x-heroicon-o-cheveron-down class="w-4 h-4" />
        </x-slot>
        {{ $slot }}
    </x-filament-dropdown>
</div>
