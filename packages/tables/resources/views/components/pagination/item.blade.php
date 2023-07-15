@props([
    'active' => false,
    'disabled' => false,
    'icon' => false,
    'label' => null,
    'separator' => false,
])

<li>
    <button
        {{
            $attributes
                ->merge([
                    'disabled' => $disabled || $separator,
                    'type' => 'button',
                ], escape: false)
                ->class([
                    'fi-ta-pagination-item relative -my-3 flex h-8 min-w-[2rem] items-center justify-center rounded-md px-1.5 font-medium outline-none disabled:pointer-events-none disabled:opacity-70',
                    'hover:bg-gray-500/5 focus:bg-primary-500/10 focus:ring-2 focus:ring-primary-500 dark:hover:bg-gray-400/5' => (! $active) && (! $disabled) && (! $separator),
                    'focus:text-primary-600' => (! $active) && (! $disabled) && (! $icon) && (! $separator),
                    'transition' => ((! $active) && (! $disabled) && (! $separator)) || $active,
                    'text-primary-600' => ((! $active) && (! $disabled) && $icon && (! $separator)) || $active,
                    'fi-ta-pagination-item-active bg-primary-500/10 ring-2 ring-primary-500 focus:underline' => $active,
                    'fi-ta-pagination-item-disabled' => $disabled,
                    'fi-ta-pagination-item-separator cursor-default' => $separator,
                ])
        }}
    >
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                class="fi-ta-pagination-item-icon h-5 w-5 rtl:-scale-x-100"
            />
        @endif

        <span>{{ $label ?? ($separator ? '...' : '') }}</span>
    </button>
</li>
