@isset ($dropdown)
    <div @click.away="open = false" x-data="{ open: false }" {{ $attributes->merge(['class' => 'btn btn-with-dropdown']) }}>
        <button class="btn-primary-action">{{ __($label) }}</button>
        <div class="btn-dropdown">
            <button @click.prevent="open = !open" class="btn-dropdown-actions">
                <span class="sr-only">{{ __('More Options') }}</span>
                {{ Filament::svg('heroicons/outline-md/md-cheveron-down', 'w-auto h-4') }}
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="btn-dropdown-content">
                {{ $dropdown }}
            </div>
        </div>
    </div>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn']) }}>{{ __($label) }}</button>
@endisset
