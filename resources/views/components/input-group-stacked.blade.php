@props([
    'name',
    'label' => $name,
    'hideLabel' => false,
    'required' => false,
])

<div>
    <x-filament::label :for="$name" :hidden="$hideLabel" class="block mb-2">
        {{ $label }}
        @if ($required)
            <sup class="text-red-600">*</sup>
        @endif
    </x-filament::label>
    {{ $slot }}
    <x-filament::error :name="$name" class="mt-2" />
</div>