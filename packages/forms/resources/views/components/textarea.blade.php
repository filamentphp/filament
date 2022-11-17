<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
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
                ->merge($getExtraAttributes(), escape: true)
                ->merge($getExtraInputAttributes(), escape: true)
                ->class([
                    'filament-forms-textarea-component block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-primary-500',
                    'border-gray-300' => ! $errors->has($getStatePath()),
                    'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()),
                ])
        }}
        @if ($shouldAutosize())
            x-ignore
            ax-load
            ax-load-src="/js/filament/forms/components/textarea.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
            x-data="textareaFormComponent()"
            x-on:input="render()"
            style="height: 150px"
            {{ $getExtraAlpineAttributeBag() }}
        @endif
    ></textarea>
</x-dynamic-component>
