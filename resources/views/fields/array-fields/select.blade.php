<div class="col-md{{ $array_field->column_width ? '-' . $array_field->column_width : '' }} mb-2 mb-md-0">
    <select
        class="custom-select custom-select-sm @error($field->key . '.' . $key . '.' . $array_field->name) is-invalid @enderror"
        wire:model.lazy="{{ $field->key . '.' . $key . '.' . $array_field->name }}">

        <option value="">{{ $array_field->placeholder }}</option>

        @foreach($array_field->options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    @include('filament::fields.array-fields.error-help')
</div>
