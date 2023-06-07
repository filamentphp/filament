@props([
    'active' => false,
    'disabled' => false,
    'icon' => false,
    'label' => null,
    'separator' => false,
])

<li>
    <button
        @if ($disabled || $separator) disabled @endif
        type="button"
        {{
            $attributes->class([
                'filament-tables-pagination-item relative -my-3 flex h-8 min-w-[2rem] items-center justify-center rounded-md px-1.5 font-medium outline-none',
                'hover:bg-gray-500/5 focus:bg-primary-500/10 focus:ring-2 focus:ring-primary-500' => (! $active) && (! $disabled) && (! $separator),
                'dark:hover:bg-gray-400/5' => (! $active) && (! $disabled) && (! $separator) && config('tables.dark_mode'),
                'focus:text-primary-600' => (! $active) && (! $disabled) && (! $icon) && (! $separator),
                'transition' => ((! $active) && (! $disabled) && (! $separator)) || $active,
                'text-primary-600' => ((! $active) && (! $disabled) && $icon && (! $separator)) || $active,
                'filament-tables-pagination-item-active bg-primary-500/10 ring-2 ring-primary-500 focus:underline' => $active,
                'filament-tables-pagination-item-disabled pointer-events-none cursor-not-allowed opacity-70' => $disabled,
                'filament-tables-pagination-item-separator cursor-default' => $separator,
            ])
        }}
    >
        @if ($icon)
            <x-dynamic-component
                :component="$icon"
                class="h-5 w-5 rtl:scale-x-[-1]"
            />
        @endif

        <span>{{ $label ?? ($separator ? '...' : '') }}</span>
    </button>
</li>
