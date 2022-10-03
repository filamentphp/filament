@props([
    'icon' => null,
    'hintAction' => null,
])

<div {{ $attributes->class([
    'filament-forms-field-wrapper-hint flex items-center space-x-2 rtl:space-x-reverse text-gray-500',
    'dark:text-gray-300' => config('forms.dark_mode'),
]) }}>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif

    @if ($hintAction && ! $hintAction->isHidden())
        <div class="filament-forms-field-wrapper-hint-action contents">{{ $hintAction }}</div>
    @endif
</div>
