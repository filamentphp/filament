<div class="mb-5">
    <x-filament-checkbox type="radio" :name="$field->key" :label="$field->placeholder ?? $field->label" :value="$field->value" :model="$field->key" />
    @include('filament::fields.error-help')
</div>

