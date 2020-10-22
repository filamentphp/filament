@props([
    'name',
    'label',
    'hint' => false,
    'help' => false,
    'required' => false,
    'errorClasses' => $errors->has($name) ? 'motion-safe:animate-shake' : '',
])

<div {{ $attributes->merge(['class' => $errorClasses]) }}>
    <div class="flex items-center justify-between mb-2 space-x-2">
        <x-filament::label :for="$name">
            {{ $label ?? $name }}
            @if ($required)
                <sup class="text-red-600">*</sup>
            @endif
        </x-filament::label>
        @if ($hint)
            <x-filament::hint>
                {{ $hint }}
            </x-filament::hint>
        @endif
    </div>
    {{ $slot }}
    <x-filament::error :name="$name" class="mt-1" />
    @if ($help)
        <x-filament::help class="mt-1">
            {{ $help }}
        </x-filament::help>
    @endif
</div>