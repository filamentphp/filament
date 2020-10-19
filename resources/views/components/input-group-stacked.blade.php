@props([
    'name',
    'label',
    'hint' => false,
    'help' => false,
    'required' => false,
])

<div>
    <div class="flex items-center justify-between mb-2 space-x-2">
        <x-filament::label :for="$name">
            {{ $label ?? $name }}
            @if ($required)
                <sup class="text-red-600">*</sup>
            @endif
        </x-filament::label>
        @if ($hint)
            <div class="font-mono text-xs leading-tight text-gray-600">{{ $hint }}</div>
        @endif
    </div>
    {{ $slot }}
    <x-filament::error :name="$name" class="mt-2" />
    @if ($help)
        <div class="text-sm leading-tight text-gray-600 mt-2">{{ $help }}</div>
    @endif
</div>