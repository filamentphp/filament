<fieldset class="mb-5">
    <legend class="text-sm font-medium leading-5 mb-1">
        {{ $field->label }}
    </legend>
    <ol class="list-unstyled">
        @foreach ($field->options as $value => $label)
            <li class="mb-1">
                <x-filament-checkbox :name="$field->key" :label="$label" :value="$value" :model="$field->key" />
            </li>
        @endforeach
    </ol>
    @include('filament::fields.error-help')
</fieldset>
