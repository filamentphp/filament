@php
    use Filament\Support\Enums\VerticalAlignment;
@endphp

@props([
    'field' => null,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => null,
    'id' => null,
    'inlineLabelVerticalAlignment' => VerticalAlignment::Start,
    'isDisabled' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'required' => null,
    'statePath' => null,
])

@php
    if ($field) {
        $hasInlineLabel ??= $field->hasInlineLabel();
        $hasNestedRecursiveValidationRules ??= $field instanceof \Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
        $id ??= $field->getId();
        $isDisabled ??= $field->isDisabled();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $required ??= $field->isMarkedAsRequired();
        $statePath ??= $field->getStatePath();
    }

    $beforeLabelDecorations = $field?->getDecorations($field::BEFORE_LABEL_DECORATIONS);
    $afterLabelDecorations = $field?->getDecorations($field::AFTER_LABEL_DECORATIONS);
    $aboveContentDecorations = $field?->getDecorations($field::ABOVE_CONTENT_DECORATIONS);
    $belowContentDecorations = $field?->getDecorations($field::BELOW_CONTENT_DECORATIONS);
    $beforeContentDecorations = $field?->getDecorations($field::BEFORE_CONTENT_DECORATIONS);
    $afterContentDecorations = $field?->getDecorations($field::AFTER_CONTENT_DECORATIONS);
    $aboveErrorMessageDecorations = $field?->getDecorations($field::ABOVE_ERROR_MESSAGE_DECORATIONS);
    $belowErrorMessageDecorations = $field?->getDecorations($field::BELOW_ERROR_MESSAGE_DECORATIONS);

    $hasError = filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")));
@endphp

<div
    data-field-wrapper
    {{
        $attributes
            ->merge($field?->getExtraFieldWrapperAttributes() ?? [])
            ->class(['fi-fo-field-wrp'])
    }}
>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div
        @class([
            'grid gap-y-2',
            'sm:grid-cols-3 sm:gap-x-4' => $hasInlineLabel,
            match ($inlineLabelVerticalAlignment) {
                VerticalAlignment::Start => 'sm:items-start',
                VerticalAlignment::Center => 'sm:items-center',
                VerticalAlignment::End => 'sm:items-end',
            } => $hasInlineLabel,
        ])
    >
        {{ $field?->getDecorations($field::ABOVE_LABEL_DECORATIONS) }}

        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $beforeLabelDecorations || $afterLabelDecorations)
            <div
                @class([
                    'flex items-center gap-x-3',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelDecorations }}

                @if ($label && (! $labelSrOnly))
                    <x-filament-forms::field-wrapper.label
                        :for="$id"
                        :disabled="$isDisabled"
                        :prefix="$labelPrefix"
                        :required="$required"
                        :suffix="$labelSuffix"
                    >
                        {{ $label }}
                    </x-filament-forms::field-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                {{ $afterLabelDecorations }}
            </div>
        @endif

        {{ $field?->getDecorations($field::BELOW_LABEL_DECORATIONS) }}

        @if ((! \Filament\Support\is_slot_empty($slot)) || $hasError || $aboveContentDecorations || $belowContentDecorations || $beforeContentDecorations || $afterContentDecorations || $aboveErrorMessageDecorations || $belowErrorMessageDecorations)
            <div
                @class([
                    'grid auto-cols-fr gap-y-2',
                    'sm:col-span-2' => $hasInlineLabel,
                ])
            >
                {{ $aboveContentDecorations }}

                @if ($beforeContentDecorations || $afterContentDecorations)
                    <div class="flex w-full items-center gap-x-3">
                        {{ $beforeContentDecorations }}

                        <div class="w-full">
                            {{ $slot }}
                        </div>

                        {{ $afterContentDecorations }}
                    </div>
                @else
                    {{ $slot }}
                @endif

                {{ $belowContentDecorations }}

                {{ $aboveErrorMessageDecorations }}

                @if ($hasError)
                    <x-filament-forms::field-wrapper.error-message>
                        {{ $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                    </x-filament-forms::field-wrapper.error-message>
                @endif

                {{ $belowErrorMessageDecorations }}
            </div>
        @endif
    </div>
</div>
