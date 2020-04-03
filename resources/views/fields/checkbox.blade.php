<div class="mb-5">
    <x-filament-checkbox :name="$field->key" :label="$field->placeholder ?? $field->label" :value="$field->value" :model="$field->key" :disabled="$field->disabled" />
    @include('filament::fields.error-help')
</div>
