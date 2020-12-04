@props([
    'field',
    'for',
    'label',
    'hint' => false,
    'help' => false,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex items-center justify-between space-x-2">
        <x-filament::label :for="$for">
            {{ $label }}
            @if ($required)
                <sup class="text-red-600">
                    <span class="sr-only">{{ __('Required field') }}</span>*
                </sup>
            @endif
        </x-filament::label>
        @if ($hint)
            <x-filament::hint>
                {{ $hint }}
            </x-filament::hint>
        @endif
    </div>
    {{ $slot }}
    <x-filament::error :field="$field" />
    @if ($help)
        <x-filament::help>
            {{ $help }}
        </x-filament::help>
    @endif
</div>