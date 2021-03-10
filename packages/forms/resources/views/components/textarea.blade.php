<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->getId()"
    :help-message="$formComponent->helpMessage"
    :hint="$formComponent->hint"
    :label="$formComponent->label"
    :required="$formComponent->required"
>
    <textarea
        {!! $formComponent->autocomplete ? "autocomplete=\"{$formComponent->autocomplete}\"" : null !!}
        {!! $formComponent->autofocus ? 'autofocus' : null !!}
        {!! $formComponent->cols ? "cols=\"{$formComponent->cols}\"" : null !!}
        {!! $formComponent->disabled ? 'disabled' : null !!}
        {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
        {!! $formComponent->maxLength ? "maxlength=\"{$formComponent->maxLength}\"" : null !!}
        {!! $formComponent->minLength ? "minlength=\"{$formComponent->minLength}\"" : null !!}
        {!! $formComponent->name ? "{$formComponent->nameAttribute}=\"{$formComponent->name}\"" : null !!}
        {!! $formComponent->placeholder ? "placeholder=\"{$formComponent->placeholder}\"" : null !!}
        {!! $formComponent->required ? 'required' : null !!}
        {!! $formComponent->rows ? "rows=\"{$formComponent->rows}\"" : null !!}
        class="block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    ></textarea>
</x-forms::field-group>
