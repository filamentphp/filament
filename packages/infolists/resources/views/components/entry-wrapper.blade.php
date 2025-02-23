@props([
    'alignment' => null,
    'entry' => null,
    'hasInlineLabel' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
])

@php
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    if ($entry) {
        $alignment ??= $entry->getAlignment();
        $hasInlineLabel ??= $entry->hasInlineLabel();
        $label ??= $entry->getLabel();
        $labelSrOnly ??= $entry->isLabelHidden();
    }

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $beforeLabelContainer = $entry?->getChildSchema($entry::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $afterLabelContainer = $entry?->getChildSchema($entry::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
    $beforeContentContainer = $entry?->getChildSchema($entry::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $afterContentContainer = $entry?->getChildSchema($entry::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();
@endphp

<div
    {{
        $attributes
            ->merge($entry?->getExtraEntryWrapperAttributes() ?? [], escape: false)
            ->class([
                'fi-in-entry',
                'fi-in-entry-has-inline-label' => $hasInlineLabel,
            ])
    }}
>
    @if ($label && $labelSrOnly)
        <dt class="fi-in-entry-label fi-sr-only">
            {{ $label }}
        </dt>
    @endif

    <div class="fi-in-entry-label-col">
        {{ $entry?->getChildSchema($entry::ABOVE_LABEL_SCHEMA_KEY) }}

        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $beforeLabelContainer || $afterLabelContainer)
            <div
                @class([
                    'fi-in-entry-label-ctn',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelContainer }}

                {{ $labelPrefix }}

                @if ($label && (! $labelSrOnly))
                    <dt
                        {{
                            (
                                ($label instanceof \Illuminate\View\ComponentSlot)
                                ? $label->attributes
                                : (new ComponentAttributeBag)
                            )
                                ->class(['fi-in-entry-label'])
                        }}
                    >
                        {{ $label }}
                    </dt>
                @endif

                {{ $labelSuffix }}

                {{ $afterLabelContainer }}
            </div>
        @endif

        {{ $entry?->getChildSchema($entry::BELOW_LABEL_SCHEMA_KEY) }}
    </div>

    <div class="fi-in-entry-content-col">
        {{ $entry?->getChildSchema($entry::ABOVE_CONTENT_SCHEMA_KEY) }}

        <div class="fi-in-entry-content-ctn">
            {{ $beforeContentContainer }}

            <dd
                @class([
                    'fi-in-entry-content',
                    (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
                ])
            >
                {{ $slot }}
            </dd>

            {{ $afterContentContainer }}
        </div>

        {{ $entry?->getChildSchema($entry::BELOW_CONTENT_SCHEMA_KEY) }}
    </div>
</div>
