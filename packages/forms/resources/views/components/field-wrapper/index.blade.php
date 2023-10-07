@props([
    'field' => null,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => null,
    'helperText' => null,
    'hint' => null,
    'hintActions' => null,
    'hintColor' => null,
    'hintIcon' => null,
    'hintIconTooltip' => null,
    'id' => null,
    'isDisabled' => null,
    'isMarkedAsRequired' => null,
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
        $helperText ??= $field->getHelperText();
        $hint ??= $field->getHint();
        $hintActions ??= $field->getHintActions();
        $hintColor ??= $field->getHintColor();
        $hintIcon ??= $field->getHintIcon();
        $hintIconTooltip ??= $field->getHintIconTooltip();
        $id ??= $field->getId();
        $isDisabled ??= $field->isDisabled();
        $isMarkedAsRequired ??= $field->isMarkedAsRequired();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $required ??= $field->isRequired();
        $statePath ??= $field->getStatePath();
    }

    $hintActions = array_filter(
        $hintActions ?? [],
        fn (\Filament\Forms\Components\Actions\Action $hintAction): bool => $hintAction->isVisible(),
    );

    $hasError = $errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*"));
@endphp

<div {{ $attributes->class(['fi-fo-field-wrp']) }}>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div
        @class([
            'grid gap-y-2',
            'sm:grid-cols-3 sm:items-start sm:gap-x-4' => $hasInlineLabel,
        ])
    >
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || filled($hint) || $hintIcon || count($hintActions))
            <div
                @class([
                    'flex items-center justify-between gap-x-3',
                    'sm:pt-1.5' => $hasInlineLabel,
                ])
            >
                @if ($label && (! $labelSrOnly))
                    <x-filament-forms::field-wrapper.label
                        :for="$id"
                        :error="$errors->has($statePath)"
                        :is-disabled="$isDisabled"
                        :is-marked-as-required="$isMarkedAsRequired"
                        :prefix="$labelPrefix"
                        :suffix="$labelSuffix"
                        :required="$required"
                    >
                        {{ $label }}
                    </x-filament-forms::field-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                @if (filled($hint) || $hintIcon || count($hintActions))
                    <x-filament-forms::field-wrapper.hint
                        :actions="$hintActions"
                        :color="$hintColor"
                        :icon="$hintIcon"
                        :tooltip="$hintIconTooltip"
                    >
                        {{ $hint }}
                    </x-filament-forms::field-wrapper.hint>
                @endif
            </div>
        @endif

        @if ((! \Filament\Support\is_slot_empty($slot)) || $hasError || filled($helperText))
            <div
                @class([
                    'grid gap-y-2',
                    'sm:col-span-2' => $hasInlineLabel,
                ])
            >
                {{ $slot }}

                @if ($hasError)
                    <x-filament-forms::field-wrapper.error-message>
                        {{ $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                    </x-filament-forms::field-wrapper.error-message>
                @endif

                @if (filled($helperText))
                    <x-filament-forms::field-wrapper.helper-text>
                        {{ $helperText }}
                    </x-filament-forms::field-wrapper.helper-text>
                @endif
            </div>
        @endif
    </div>
</div>
