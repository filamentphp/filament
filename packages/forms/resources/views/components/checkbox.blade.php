<x-forms::field-group
    :column-span="$formComponent->columnSpan"
    :error-key="$formComponent->name"
    :for="$formComponent->getId()"
    :help-message="__($formComponent->helpMessage)"
    :hint="__($formComponent->hint)"
    :label="__($formComponent->label)"
    :required="$formComponent->required"
>
    <x-slot name="labelPrefix">
        <input
            {!! $formComponent->autofocus ? 'autofocus' : null !!}
            {!! $formComponent->disabled ? 'disabled' : null !!}
            {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
            {!! $formComponent->name ? "{$formComponent->nameAttribute}=\"{$formComponent->name}\"" : null !!}
            type="checkbox"
            {!! $formComponent->required ? 'required' : null !!}
            class="rounded text-primary-600 shadow-sm focus:border-primary-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->name) ? 'border-danger-600 ' : 'border-gray-300' }}"
            {!! Filament\format_attributes($formComponent->extraAttributes) !!}
        />
    </x-slot>
</x-forms::field-group>
