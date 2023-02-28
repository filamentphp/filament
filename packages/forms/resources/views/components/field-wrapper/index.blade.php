@props([
    'field' => null,
    'id' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'hasNestedRecursiveValidationRules' => false,
    'helperText' => null,
    'hint' => null,
    'hintActions' => null,
    'hintColor' => null,
    'hintIcon' => null,
    'isDisabled' => null,
    'isMarkedAsRequired' => null,
    'required' => null,
    'statePath' => null,
])

@php
    if ($field) {
        $id ??= $field->getId();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $hasNestedRecursiveValidationRules ??= $field instanceof \Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
        $helperText ??= $field->getHelperText();
        $hint ??= $field->getHint();
        $hintActions ??= $field->getHintActions();
        $hintColor ??= $field->getHintColor();
        $hintIcon ??= $field->getHintIcon();
        $isDisabled ??= $field->isDisabled();
        $isMarkedAsRequired ??= $field->isMarkedAsRequired();
        $required ??= $field->isRequired();
        $statePath ??= $field->getStatePath();
    }

    $hintActions = array_filter(
        $hintActions ?? [],
        fn (\Filament\Forms\Components\Actions\Action $hintAction): bool => $hintAction->isVisible(),
    );
@endphp

<div {{ $attributes->class(['filament-forms-field-wrapper']) }}>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div class="space-y-2">
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $hint || $hintIcon || count($hintActions))
            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                @if ($label && (! $labelSrOnly))
                    <x-filament-forms::field-wrapper.label
                        :for="$id"
                        :error="$errors->has($statePath)"
                        :is-disabled="$isDisabled"
                        :is-marked-as-required="$isMarkedAsRequired"
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

                @if ($hint || $hintIcon || count($hintActions))
                    <x-filament-forms::field-wrapper.hint :actions="$hintActions" :color="$hintColor" :icon="$hintIcon">
                        {{ filled($hint) ? ($hint instanceof \Illuminate\Support\HtmlString ? $hint : str($hint)->markdown()->sanitizeHtml()->toHtmlString()) : null }}
                    </x-filament-forms::field-wrapper.hint>
                @endif
            </div>
        @endif

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
