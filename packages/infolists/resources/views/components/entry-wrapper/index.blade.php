@props([
    'action' => null,
    'alignment' => null,
    'entry' => null,
    'hasInlineLabel' => null,
    'helperText' => null,
    'hint' => null,
    'hintActions' => null,
    'hintColor' => null,
    'hintIcon' => null,
    'id' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'shouldOpenUrlInNewTab' => null,
    'statePath' => null,
    'tooltip' => null,
    'url' => null,
])

@php
    if ($entry) {
        $action ??= $entry->getAction();
        $alignment ??= $entry->getAlignment();
        $hasInlineLabel ??= $entry->hasInlineLabel();
        $helperText ??= $entry->getHelperText();
        $hint ??= $entry->getHint();
        $hintActions ??= $entry->getHintActions();
        $hintColor ??= $entry->getHintColor();
        $hintIcon ??= $entry->getHintIcon();
        $id ??= $entry->getId();
        $label ??= $entry->getLabel();
        $labelSrOnly ??= $entry->isLabelHidden();
        $shouldOpenUrlInNewTab ??= $entry->shouldOpenUrlInNewTab();
        $statePath ??= $entry->getStatePath();
        $tooltip ??= $entry->getTooltip();
        $url ??= $entry->getUrl();
    }

    $hintActions = array_filter(
        $hintActions ?? [],
        fn (\Filament\Infolists\Components\Actions\Action $hintAction): bool => $hintAction->isVisible(),
    );
@endphp

<div {{ $attributes->class(['fi-in-entry-wrp']) }}>
    @if ($label && $labelSrOnly)
        <dt class="sr-only">
            {{ $label }}
        </dt>
    @endif

    <div
        @class([
            'grid gap-y-2',
            'sm:grid-cols-3 sm:items-start sm:gap-x-4' => $hasInlineLabel,
        ])
    >
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || filled($hint) || $hintIcon)
            <div class="flex items-center justify-between gap-x-3">
                @if ($label && (! $labelSrOnly))
                    <x-filament-infolists::entry-wrapper.label
                        :prefix="$labelPrefix"
                        :suffix="$labelSuffix"
                    >
                        {{ $label }}
                    </x-filament-infolists::entry-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                @if (filled($hint) || $hintIcon || count($hintActions))
                    <x-filament-infolists::entry-wrapper.hint
                        :actions="$hintActions"
                        :color="$hintColor"
                        :icon="$hintIcon"
                    >
                        {{ $hint }}
                    </x-filament-infolists::entry-wrapper.hint>
                @endif
            </div>
        @endif

        <div
            @class([
                'grid gap-y-2',
                'sm:col-span-2' => $hasInlineLabel,
            ])
        >
            <dd
                @if ($tooltip)
                    x-data="{}"
                    x-tooltip="{
                        content: @js($tooltip),
                        theme: $store.theme,
                    }"
                @endif
                @class([
                    match ($alignment) {
                        'center' => 'text-center',
                        'end' => 'text-end',
                        'justify' => 'text-justify',
                        'left' => 'text-left',
                        'right' => 'text-right',
                        'start' => 'text-start',
                        default => null,
                    },
                ])
            >
                @if ($url)
                    <a
                        href="{{ $url }}"
                        @if ($shouldOpenUrlInNewTab)
                            target="_blank"
                        @endif
                        class="block"
                    >
                        {{ $slot }}
                    </a>
                @elseif ($action)
                    @php
                        $wireClickAction = $action->getLivewireClickHandler();
                    @endphp

                    <button
                        type="button"
                        wire:click="{{ $wireClickAction }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $wireClickAction }}"
                        class="block"
                    >
                        {{ $slot }}
                    </button>
                @else
                    {{ $slot }}
                @endif
            </dd>

            @if (filled($helperText))
                <x-filament-infolists::entry-wrapper.helper-text>
                    {{ $helperText }}
                </x-filament-infolists::entry-wrapper.helper-text>
            @endif
        </div>
    </div>
</div>
