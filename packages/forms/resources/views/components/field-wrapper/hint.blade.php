@props([
    'icon' => null,
])

<div {{ $attributes->class(['flex space-x-2 text-gray-500', 'filament-forms-field-wrapper-hint']) }}>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif
</div>
