@props([
    'field' => null,
    'id' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'helperText' => null,
    'hint' => null,
    'hintAction' => null,
    'hintColor' => null,
    'hintIcon' => null,
    'required' => null,
    'shouldBeMarkedAsRequired' => null,
    'statePath' => null,
])

@php
    $id ??= $field->getId();
    $label ??= $field->getLabel();
    $labelSrOnly ??= $field->isLabelHidden();
    $helperText ??= $field->getHelperText();
    $hint ??= $field->getHint();
    $hintAction ??= $field->getHintAction();
    $hintColor ??= $field->getHintColor();
    $hintIcon ??= $field->getHintIcon();
    $required ??= $field->isRequired();
    $shouldBeMarkedAsRequired ??= $field->shouldBeMarkedAsRequired();
    $statePath ??= $field->getStatePath();
@endphp

<div {{ $attributes->class(['filament-forms-field-wrapper']) }}>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div class="space-y-2">
        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $hint || $hintIcon || $hintAction)
            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                @if ($label && (! $labelSrOnly))
                    <x-forms::field-wrapper.label
                        :for="$id"
                        :error="$errors->has($statePath)"
                        :prefix="$labelPrefix"
                        :required="$required"
                        :should-be-marked-required="$shouldBeMarkedAsRequired"
                        :suffix="$labelSuffix"
                    >
                        {{ $label }}
                    </x-forms::field-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                @if ($hint || $hintIcon || $hintAction)
                    <x-forms::field-wrapper.hint :action="$hintAction" :color="$hintColor" :icon="$hintIcon">
                        {{ filled($hint) ? ($hint instanceof \Illuminate\Support\HtmlString ? $hint : str($hint)->markdown()->sanitizeHtml()->toHtmlString()) : null }}
                    </x-forms::field-wrapper.hint>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($errors->has($statePath))
            <x-forms::field-wrapper.error-message>
                {{ $errors->first($statePath) }}
            </x-forms::field-wrapper.error-message>
        @endif

        @if ($helperText)
            <x-forms::field-wrapper.helper-text>
                {{ $helperText instanceof \Illuminate\Support\HtmlString ? $helperText : str($helperText)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-forms::field-wrapper.helper-text>
        @endif
    </div>
</div>
