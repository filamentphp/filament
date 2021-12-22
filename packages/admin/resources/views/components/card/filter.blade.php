@props([
    'filter' => null,
])
@if ($filter)
    <div {{ $attributes->class(['flex items-center justify-between']) }}>
        <x-filament::card.heading>
            {{ $this->getHeading() }}
        </x-filament::card.heading>
        <select wire:model='filter'
            class="font-medium text-sm text-primary block w-1/2 transition duration-75 rounded-lg shadow-sm border-gray-300 focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600">
            @foreach ($this->getFilters() as $value => $label)
                <option value="{{ $value }}">
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <x-filament::hr />
@else
    <x-slot name="heading">
        {{ $this->getHeading() }}
    </x-slot>
@endif
