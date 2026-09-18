@props([
    'active' => false,
    'ariaLabel' => null,
    'disabled' => false,
    'icon' => null,
    'iconAlias' => null,
    'label' => null,
])

@php
    use Filament\Support\View\ComponentAttributeBag;
    use Illuminate\Support\Number;
@endphp

<li
    {{
        $attributes->class([
            'fi-pagination-item',
            'fi-disabled' => $disabled,
            'fi-active' => $active,
        ])
    }}
>
    <button
        @if (filled($ariaLabel))
            aria-label="{{ $ariaLabel }}"
        @endif
        @if ($active)
            aria-current="page"
        @endif
        @if ($disabled)
            aria-hidden="true"
        @endif
        @disabled($disabled)
        type="button"
        class="fi-pagination-item-btn"
    >
        @if ($icon || $iconAlias)
            {{
                \Filament\Support\generate_icon_html($icon, $iconAlias, attributes: (new ComponentAttributeBag)->merge(['aria-hidden' => 'true'], escape: false)->class([
                    'fi-pagination-item-icon',
                ]))
            }}
        @endif

        @if (filled($label))
            <span class="fi-pagination-item-label">
                {{ is_numeric($label) ? Number::format($label) : ($label ?? '...') }}
            </span>
        @endif
    </button>
</li>
