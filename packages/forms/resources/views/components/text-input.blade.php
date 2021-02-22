<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->id"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
>
    <input
        {!! $formComponent->autocomplete ? "autocomplete=\"{$formComponent->autocomplete}\"" : null !!}
        {!! $formComponent->autofocus ? 'autofocus' : null !!}
        {!! $formComponent->disabled ? 'disabled' : null !!}
        {!! $formComponent->id ? "id=\"{$formComponent->id}\"" : null !!}
        {!! $formComponent->name ? "{$formComponent->nameAttribute}=\"{$formComponent->name}\"" : null !!}
        {!! $formComponent->maxLength ? "maxlength=\"{$formComponent->maxLength}\"" : null !!}
        {!! $formComponent->minLength ? "minlength=\"{$formComponent->minLength}\"" : null !!}
        {!! $formComponent->placeholder ? "placeholder=\"{$formComponent->placeholder}\"" : null !!}
        {!! $formComponent->required ? 'required' : null !!}
        {!! $formComponent->type ? "type=\"{$formComponent->type}\"" : null !!}
        class="block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-secondary-300 focus:ring focus:ring-secondary-200 focus:ring-opacity-50 {{ $errors->has($formComponent->name) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        {!! Filament\format_attributes($formComponent->extraAttributes) !!}
    />
</x-forms::field-group>
