@pushonce('head:flatpickr-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpushonce

@pushonce('js:flatpickr')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpushonce

<x-filament::field-group
    :errorKey="$field->errorKey"
    :for="$field->id"
    :help="__($field->help)"
    :hint="__($field->hint)"
    :label="__($field->label)"
    :required="$field->required"
>
    <div
        wire:ignore
        x-data
        x-init='flatpickr($refs.input, @json($field->config))'
    >
        <x-filament::input
            type="date"
            :value="$field->value"
            :id="$field->id"
            :name="$field->name"
            :name-attribute="$field->nameAttribute"
            :error-key="$field->errorKey"
            :extra-attributes="$field->attributes"
            x-ref="input"
        />
    </div>
</x-filament::field-group>
