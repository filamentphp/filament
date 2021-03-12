<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div class="flex border-gray-300 rounded shadow-sm">
        @if ($formComponent->getPrefix())
            <span class="inline-flex items-center px-3 text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 sm:text-sm">
                {{ $formComponent->getPrefix() }}
            </span>
        @endif

        <input
            {!! $formComponent->getAutocomplete() ? "autocomplete=\"{$formComponent->getAutocomplete()}\"" : null !!}
            {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
            {!! $formComponent->isDisabled() ? 'disabled' : null !!}
            {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
            {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
            {!! $formComponent->getMaxLength() ? "maxlength=\"{$formComponent->getMaxLength()}\"" : null !!}
            {!! $formComponent->getMinLength() ? "minlength=\"{$formComponent->getMinLength()}\"" : null !!}
            {!! $formComponent->getPlaceholder() ? "placeholder=\"{$formComponent->getPlaceholder()}\"" : null !!}
            {!! $formComponent->isRequired() ? 'required' : null !!}
            {!! $formComponent->getType() ? "type=\"{$formComponent->getType()}\"" : null !!}
            class="block w-full rounded-r-md placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ ! $formComponent->getPrefix() ? 'rounded-l-md' : null }} {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
            {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
        />
    </div>
</x-forms::field-group>
