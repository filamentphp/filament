<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <textarea
        {!! $formComponent->getAutocomplete() ? "autocomplete=\"{$formComponent->getAutocomplete()}\"" : null !!}
        {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
        {!! $formComponent->getCols() ? "cols=\"{$formComponent->getCols()}\"" : null !!}
        {!! $formComponent->isDisabled() ? 'disabled' : null !!}
        {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
        {!! $formComponent->getMaxLength() ? "maxlength=\"{$formComponent->getMaxLength()}\"" : null !!}
        {!! $formComponent->getMinLength() ? "minlength=\"{$formComponent->getMinLength()}\"" : null !!}
        {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
        {!! $formComponent->getPlaceholder() ? "placeholder=\"{$formComponent->getPlaceholder()}\"" : null !!}
        {!! $formComponent->isRequired() ? 'required' : null !!}
        {!! $formComponent->getRows() ? "rows=\"{$formComponent->getRows()}\"" : null !!}
        class="block w-full rounded shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 resize-none {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
        {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
    ></textarea>
</x-forms::field-group>
