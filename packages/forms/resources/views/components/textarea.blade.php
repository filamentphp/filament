<x-dynamic-component
    :component="$getFieldWrapperView()"
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
        dusk="filament.forms.{{ $getStatePath() }}"
        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
        {!! ($rows = $getRows()) ? "rows=\"{$rows}\"" : null !!}
        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
        @if (! $isConcealed())
            {!! filled($length = $getMaxLength()) ? "maxlength=\"{$length}\"" : null !!}
            {!! filled($length = $getMinLength()) ? "minlength=\"{$length}\"" : null !!}
            {!! $isRequired() ? 'required' : null !!}
        @endif
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->merge($getExtraInputAttributeBag()->getAttributes())
                ->class([
                    'filament-forms-textarea-component block w-full rounded-lg border-none px-3 py-2 shadow-sm ring-1 ring-inset transition duration-75 focus:ring-2 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 sm:py-2.5 sm:text-sm',
                    'dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-primary-500' => config('forms.dark_mode'),
                    'ring-gray-300' => ! $errors->has($getStatePath()),
                    'ring-danger-500' => $errors->has($getStatePath()),
                ])
        }}
        @if ($shouldAutosize())
            x-data="textareaFormComponent()"
            x-on:input="render()"
            style="height: 150px"
            {{ $getExtraAlpineAttributeBag() }}
        @endif
    ></textarea>
</x-dynamic-component>
