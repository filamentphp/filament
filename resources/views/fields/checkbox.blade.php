<div class="col-span-4 {{ $field->class }}">
    <x-filament-checkbox :name="$field->name" :label="$field->placeholder ?? $field->label" :value="$field->value" :model="$field->key" :disabled="$field->disabled" />
    @include('filament::fields.error-help')
</div>
