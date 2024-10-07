@props([
    'action' => null,
    'alignment' => null,
    'entry' => null,
    'hasInlineLabel' => null,
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
    use Filament\Support\Enums\Alignment;

    if ($entry) {
        $action ??= $entry->getAction();
        $alignment ??= $entry->getAlignment();
        $hasInlineLabel ??= $entry->hasInlineLabel();
        $id ??= $entry->getId();
        $label ??= $entry->getLabel();
        $labelSrOnly ??= $entry->isLabelHidden();
        $shouldOpenUrlInNewTab ??= $entry->shouldOpenUrlInNewTab();
        $statePath ??= $entry->getStatePath();
        $tooltip ??= $entry->getTooltip();
        $url ??= $entry->getUrl();
    }

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $beforeLabelDecorations = $entry?->getDecorations($entry::BEFORE_LABEL_DECORATIONS);
    $afterLabelDecorations = $entry?->getDecorations($entry::AFTER_LABEL_DECORATIONS);
    $beforeContentDecorations = $entry?->getDecorations($entry::BEFORE_CONTENT_DECORATIONS);
    $afterContentDecorations = $entry?->getDecorations($entry::AFTER_CONTENT_DECORATIONS);
@endphp

<div
    {{
        $attributes
            ->merge($entry?->getExtraEntryWrapperAttributes() ?? [])
            ->class(['fi-in-entry-wrp'])
    }}
>
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
        {{ $entry?->getDecorations($entry::ABOVE_LABEL_DECORATIONS) }}

        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $beforeLabelDecorations || $afterLabelDecorations)
            <div
                @class([
                    'flex items-center gap-x-3',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelDecorations }}

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

                {{ $afterLabelDecorations }}
            </div>
        @endif

        {{ $entry?->getDecorations($entry::BELOW_LABEL_DECORATIONS) }}

        <div
            @class([
                'grid auto-cols-fr gap-y-2',
                'sm:col-span-2' => $hasInlineLabel,
            ])
        >
            {{ $entry?->getDecorations($entry::ABOVE_CONTENT_DECORATIONS) }}

            @capture($content)
                <dd
                    @if (filled($tooltip))
                        x-tooltip="{
                            content: @js($tooltip),
                            theme: $store.theme,
                        }"
                    @endif
                    @class([
                        match ($alignment) {
                            Alignment::Start => 'text-start',
                            Alignment::Center => 'text-center',
                            Alignment::End => 'text-end',
                            Alignment::Justify, Alignment::Between => 'text-justify',
                            Alignment::Left => 'text-left',
                            Alignment::Right => 'text-right',
                            default => $alignment,
                        },
                    ])
                >
                    @if ($url)
                        <a
                            {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
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
            @endcapture

            @if ($beforeContentDecorations || $afterContentDecorations)
                <div class="flex w-full items-center gap-x-3">
                    {{ $beforeContentDecorations }}

                    <div class="w-full">
                        {{ $content() }}
                    </div>

                    {{ $afterContentDecorations }}
                </div>
            @else
                {{ $content() }}
            @endif

            {{ $entry?->getDecorations($entry::BELOW_CONTENT_DECORATIONS) }}
        </div>
    </div>
</div>
