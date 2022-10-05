@props([
    'icon' => null,
    'tooltip' => false,
])

<div {{ $attributes->class([
    'filament-forms-field-wrapper-hint flex space-x-2 rtl:space-x-reverse text-gray-500',
    'dark:text-gray-300' => config('forms.dark_mode'),
]) }}>
    @if (! $tooltip && $slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($tooltip)
        <x-dynamic-component
            :component="$icon ?? 'heroicon-s-information-circle'"
            class="h-4 w-4"
            x-data="{ tooltip: {} }"
            x-init="
                Alpine.effect(() => {
                    if (Alpine.store('sidebar').isOpen) {
                        tooltip = false
                    } else {
                        tooltip = {
                            content: {{ \Illuminate\Support\Js::from($slot->toHtml()) }},
                            theme: Alpine.store('theme') === 'light' ? 'dark' : 'light',
                            placement: document.dir === 'rtl' ? 'left' : 'right',
                        }
                    }
                })
            "
            x-tooltip.html="tooltip"
        />
    @elseif ($icon)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif
</div>
