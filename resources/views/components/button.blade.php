@isset ($dropdown)
    <div {{ $attributes->merge(['class' => 'btn inline-flex p-0']) }}>
        <button type="{{ $type }}" class="px-5 py-2 rounded-l transition duration-150 ease-in-out hover:bg-indigo-800">{{ __($label) }}</button>
        <x-filament-dropdown button-class="p-2 bg-indigo-600 rounded-r transition duration-150 ease-in-out">
            <x-slot name="button">
                <span class="sr-only">{{ __('More Options') }}</span>
                <x-heroicon-o-cheveron-down class="w-auto h-4" />
            </x-slot>
            {{ $dropdown }}
        </x-filament-dropdown>
    </div>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn']) }}>{{ __($label) }}</button>
@endisset
