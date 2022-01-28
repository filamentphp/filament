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
    <textarea
        {!! ($autocapitalize = $getAutocapitalize()) ? "autocapitalize=\"{$autocapitalize}\"" : null !!}
        {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
        {!! $isAutofocused() ? 'autofocus' : null !!}
        {!! ($cols = $getCols()) ? "cols=\"{$cols}\"" : null !!}
        {!! $isDisabled() ? 'disabled' : null !!}
        id="{{ $getId() }}"
        {!! ($length = $getMaxLength()) ? "maxlength=\"{$length}\"" : null !!}
        {!! ($length = $getMinLength()) ? "minlength=\"{$length}\"" : null !!}
        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
        {!! $isRequired() ? 'required' : null !!}
        {!! ($rows = $getRows()) ? "rows=\"{$rows}\"" : null !!}
        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->merge($getExtraInputAttributeBag()->getAttributes())
                ->class([
                    'block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600' => $errors->has($getStatePath()),
                    'filament-forms-textarea-component'
                ])
        }}
        @if ($shouldAutosize())
            x-data="textareaFormComponent()"
            x-on:input="render()"
            {{ $getExtraAlpineAttributeBag() }}
        @endif
    ></textarea>
</x-forms::field-wrapper>
