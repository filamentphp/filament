<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <x-slot name="labelPrefix">
        <input
            {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
            {!! $formComponent->isDisabled() ? 'disabled' : null !!}
            {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
            {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
            type="checkbox"
            {!! $formComponent->isRequired() ? 'required' : null !!}
            class="rounded text-primary-600 shadow-sm focus:border-primary-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 ' : 'border-gray-300' }}"
            {!! Filament\format_attributes($formComponent->getExtraAttributes()) !!}
        />
    </x-slot>
</x-forms::field-group>
