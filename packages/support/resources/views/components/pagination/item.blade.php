@props([
    'active' => false,
    'ariaLabel' => null,
    'disabled' => false,
    'icon' => null,
    'iconAlias' => null,
    'label' => null,
])

<li
    {{
        $attributes->class([
            'fi-pagination-item group/item border-x-[0.5px] border-gray-200 first:border-s-0 last:border-e-0 dark:border-white/10',
            'fi-disabled' => $disabled,
            'fi-active' => $active,
        ])
    }}
>
    <button
        aria-label="{{ $ariaLabel }}"
        @disabled($disabled)
        type="button"
        @class([
            'fi-pagination-item-btn group/btn relative flex overflow-hidden p-2 outline-none transition duration-75 group-first/item:rounded-s-lg group-last/item:rounded-e-lg',
            'hover:bg-gray-50 focus-visible:z-10 focus-visible:ring-2 focus-visible:ring-primary-600 dark:hover:bg-white/5 dark:focus-visible:ring-primary-500' => ! $disabled,
            'bg-gray-50 dark:bg-white/5' => $active,
        ])
    >
        @if (filled($icon))
            {{ \Filament\Support\generate_icon_html($icon, $iconAlias, attributes: (new \Illuminate\View\ComponentAttributeBag())->class([
                'fi-pagination-item-icon size-5 text-gray-400 transition duration-75 group-hover/btn:text-gray-500 dark:text-gray-500 dark:group-hover/btn:text-gray-400',
            ])) }}
        @endif

        @if (filled($label))
            <span
                @class([
                    'fi-pagination-item-label px-1.5 text-sm font-semibold',
                    'text-gray-700 dark:text-gray-200' => ! ($disabled || $active),
                    'text-gray-500 dark:text-gray-400' => $disabled,
                    'text-primary-600 dark:text-primary-400' => $active,
                ])
            >
                {{ $label ?? '...' }}
            </span>
        @endif
    </button>
</li>
