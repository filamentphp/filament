@props([
    'active' => false,
    'ariaLabel' => null,
    'disabled' => false,
    'icon' => null,
    'iconAlias' => null,
    'label' => null,
    'loadingTargets' => [],
])

@php
    use Filament\Support\View\ComponentAttributeBag;
    use Illuminate\Support\Number;

    $wireClickAction = $attributes->whereStartsWith('wire:click')->first();
    $hasLoadingState = filled($wireClickAction) && (! $disabled);
@endphp

<li
    {{
        $attributes->whereDoesntStartWith('wire:click')->class([
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
        data-prevent-disabled-clicks
        type="button"
        {{
            $attributes->whereStartsWith('wire:click')
                ->merge([
                    'wire:loading.attr' => $hasLoadingState ? 'aria-disabled' : null,
                    'wire:loading.class' => $hasLoadingState ? 'fi-disabled' : null,
                    'wire:target' => $hasLoadingState ? (implode(',', $loadingTargets) ?: $wireClickAction) : null,
                ], escape: false)
                ->class(['fi-pagination-item-btn'])
        }}
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
