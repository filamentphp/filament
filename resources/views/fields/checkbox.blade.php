<x-filament::field-group
    :errorKey="$field->errorKey"
    :for="$field->id"
    :help="$field->help"
    :hint="$field->hint"
    :label="$field->label"
    :required="$field->required"
>
    <slot name="labelPrefix">
        <input type="{{ $field->type }}"
        {{ $field->modelDirective }}="{{ $field->name }}"
        {{ Filament\format_attributes($field->attributes) }}
        class="{{ $field->type === 'radio' ? 'rounded-full' : 'rounded' }} text-blue-700 shadow-sm focus:border-blue-700 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error($field->errorKey) border-red-600 @else border-gray-300 @enderror" />
    </slot>
</x-filament::field-group>
