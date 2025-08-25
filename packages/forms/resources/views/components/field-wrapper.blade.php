@php
    use Filament\Support\Enums\VerticalAlignment;
@endphp

@props([
    'areHtmlErrorMessagesAllowed' => null,
    'errorMessage' => null,
    'errorMessages' => null,
    'field' => null,
    'hasErrors' => true,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => null,
    'id' => null,
    'inlineLabelVerticalAlignment' => VerticalAlignment::Start,
    'isDisabled' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'labelTag' => 'label',
    'required' => null,
    'shouldShowAllValidationMessages' => null,
    'statePath' => null,
])

@php
    use Illuminate\Support\Arr;

    if ($field) {
        $hasInlineLabel ??= $field->hasInlineLabel();
        $hasNestedRecursiveValidationRules ??= $field instanceof \Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
        $id ??= $field->getId();
        $isDisabled ??= $field->isDisabled();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $required ??= $field->isMarkedAsRequired();
        $statePath ??= $field->getStatePath();
        $areHtmlErrorMessagesAllowed ??= $field->areHtmlValidationMessagesAllowed();
        $shouldShowAllValidationMessages ??= $field->shouldShowAllValidationMessages();
    }

    $aboveLabelSchema = $field?->getChildSchema($field::ABOVE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $belowLabelSchema = $field?->getChildSchema($field::BELOW_LABEL_SCHEMA_KEY)?->toHtmlString();
    $beforeLabelSchema = $field?->getChildSchema($field::BEFORE_LABEL_SCHEMA_KEY)?->toHtmlString();
    $afterLabelSchema = $field?->getChildSchema($field::AFTER_LABEL_SCHEMA_KEY)?->toHtmlString();
    $aboveContentSchema = $field?->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $belowContentSchema = $field?->getChildSchema($field::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $beforeContentSchema = $field?->getChildSchema($field::BEFORE_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $afterContentSchema = $field?->getChildSchema($field::AFTER_CONTENT_SCHEMA_KEY)?->toHtmlString();
    $aboveErrorMessageSchema = $field?->getChildSchema($field::ABOVE_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();
    $belowErrorMessageSchema = $field?->getChildSchema($field::BELOW_ERROR_MESSAGE_SCHEMA_KEY)?->toHtmlString();

    $hasError = $hasErrors && (filled($errorMessage) || filled($errorMessages) || (filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")))));

    if ($hasError && filled($statePath) && blank($errorMessage) && blank($errorMessages)) {
        if ($shouldShowAllValidationMessages) {
            $errorMessages = $errors->has($statePath) ? $errors->get($statePath) : ($hasNestedRecursiveValidationRules ? $errors->get("{$statePath}.*") : []);

            if (count($errorMessages) === 1) {
                $errorMessage = Arr::first($errorMessages);
                $errorMessages = [];
            }
        } else {
            $errorMessage = $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null);
        }
    }
@endphp

<div
    data-field-wrapper
    {{
        $attributes
            ->merge($field?->getExtraFieldWrapperAttributes() ?? [], escape: false)
            ->class([
                'fi-fo-field',
                'fi-fo-field-has-inline-label' => $hasInlineLabel,
            ])
    }}
>
    @if (filled($label) && $labelSrOnly)
        <{{ $labelTag }}
            @if ($labelTag === 'label')
                for="{{ $id }}"
            @else
                id="{{ $id }}-label"
            @endif
            class="fi-fo-field-label fi-sr-only"
        >
            {{ $label }}
        </{{ $labelTag }}>
    @endif

    @if ((filled($label) && (! $labelSrOnly)) || $hasInlineLabel || $aboveLabelSchema || $belowLabelSchema || $beforeLabelSchema || $afterLabelSchema || $labelPrefix || $labelSuffix)
        <div
            @class([
                'fi-fo-field-label-col',
                "fi-vertical-align-{$inlineLabelVerticalAlignment->value}" => $hasInlineLabel,
            ])
        >
            {{ $aboveLabelSchema }}

            <div
                @class([
                    'fi-fo-field-label-ctn',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelSchema }}

                @if ((filled($label) && (! $labelSrOnly)) || $labelPrefix || $labelSuffix)
                    <{{ $labelTag }}
                        @if ($labelTag === 'label')
                            for="{{ $id }}"
                        @else
                            id="{{ $id }}-label"
                        @endif
                        class="fi-fo-field-label"
                    >
                        {{ $labelPrefix }}

                        @if (filled($label) && (! $labelSrOnly))
                            <span class="fi-fo-field-label-content">
                                {{ $label }}@if ($required && (! $isDisabled))<sup class="fi-fo-field-label-required-mark">*</sup>
                                @endif
                            </span>
                        @endif

                        {{ $labelSuffix }}
                    </{{ $labelTag }}>
                @endif

                {{ $afterLabelSchema }}
            </div>

            {{ $belowLabelSchema }}
        </div>
    @endif

    @if ((! \Filament\Support\is_slot_empty($slot)) || $hasError || $aboveContentSchema || $belowContentSchema || $beforeContentSchema || $afterContentSchema || $aboveErrorMessageSchema || $belowErrorMessageSchema)
        <div class="fi-fo-field-content-col">
            {{ $aboveContentSchema }}

            @if ($beforeContentSchema || $afterContentSchema)
                <div class="fi-fo-field-content-ctn">
                    {{ $beforeContentSchema }}

                    <div class="fi-fo-field-content">
                        {{ $slot }}
                    </div>

                    {{ $afterContentSchema }}
                </div>
            @else
                {{ $slot }}
            @endif

            {{ $belowContentSchema }}

            @if ($hasError)
                {{ $aboveErrorMessageSchema }}

                @if (filled($errorMessages))
                    <ul
                        data-validation-error
                        class="fi-fo-field-wrp-error-list"
                    >
                        @foreach ($errorMessages as $errorMessage)
                            <li class="fi-fo-field-wrp-error-message">
                                @if ($areHtmlErrorMessagesAllowed)
                                    {!! $errorMessage !!}
                                @else
                                    {{ $errorMessage }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @elseif ($areHtmlErrorMessagesAllowed)
                    <div
                        data-validation-error
                        class="fi-fo-field-wrp-error-message"
                    >
                        {!! $errorMessage !!}
                    </div>
                @else
                    <p
                        data-validation-error
                        class="fi-fo-field-wrp-error-message"
                    >
                        {{ $errorMessage }}
                    </p>
                @endif

                {{ $belowErrorMessageSchema }}
            @endif
        </div>
    @endif
</div>
