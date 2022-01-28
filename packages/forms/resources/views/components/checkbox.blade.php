<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @if ($isInline())
        <x-slot name="labelPrefix">
    @endif
            <input
                {!! $isAutofocused() ? 'autofocus' : null !!}
                {!! $isDisabled() ? 'disabled' : null !!}
                id="{{ $getId() }}"
                type="checkbox"
                {!! $isRequired() ? 'required' : null !!}
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                {{
                    $attributes
                        ->merge($getExtraAttributes())
                        ->merge($getExtraInputAttributeBag()->getAttributes())
                        ->class([
                            'text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                            'border-gray-300' => ! $errors->has($getStatePath()),
                            'border-danger-300 ring-danger-500' => $errors->has($getStatePath()),
                            'filament-forms-checkbox-component',
                        ])
                }}
            />
    @if ($isInline())
        </x-slot>
    @endif
</x-forms::field-wrapper>
