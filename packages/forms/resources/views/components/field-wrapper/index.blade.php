@props([
    'field' => null,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => false,
    'helperText' => null,
    'hint' => null,
    'hintActions' => null,
    'hintColor' => null,
    'hintIcon' => null,
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
@endphp

<div {{ $attributes->class(['fi-fo-field-wrapper']) }}>
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
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $hint || $hintIcon || count($hintActions))
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

                @if ($hint || $hintIcon || count($hintActions))
                    <x-filament-forms::field-wrapper.hint
                        :actions="$hintActions"
                        :color="$hintColor"
                        :icon="$hintIcon"
                    >
                        {{ filled($hint) ? ($hint instanceof \Illuminate\Support\HtmlString ? $hint : str($hint)->markdown()->sanitizeHtml()->toHtmlString()) : null }}
                    </x-filament-forms::field-wrapper.hint>
                @endif
            </div>
        @endif

        <div
            @class([
                'grid gap-y-2',
                'sm:col-span-2' => $hasInlineLabel,
            ])
        >
            {{ $slot }}

            @if ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")))
                <x-filament-forms::field-wrapper.error-message>
                    {{ $errors->first($statePath) ?? ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                </x-filament-forms::field-wrapper.error-message>
            @endif

            @if ($helperText)
                <x-filament-forms::field-wrapper.helper-text>
                    {{ $helperText instanceof \Illuminate\Support\HtmlString ? $helperText : str($helperText)->markdown()->sanitizeHtml()->toHtmlString() }}
                </x-filament-forms::field-wrapper.helper-text>
            @endif
        </div>
    </div>
</div>
