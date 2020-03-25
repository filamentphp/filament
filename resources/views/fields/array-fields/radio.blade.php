<div class="col-md{{ $array_field->column_width ? '-' . $array_field->column_width : '' }} mb-2 mb-md-0">
    @foreach($array_field->options as $value => $label)
        <div class="form-check">
            <input
                id="{{ $field->key . '.' . $key . '.' . $array_field->name . '.' . $loop->index }}"
                type="radio"
                class="form-check-input @error($field->key . '.' . $key . '.' . $array_field->name) is-invalid @enderror"
                value="{{ $value }}"
                wire:model.lazy="{{ $field->key . '.' . $key . '.' . $array_field->name }}">

            <label class="form-check-label" for="{{ $field->key . '.' . $key . '.' . $array_field->name . '.' . $loop->index }}">
                {{ $label }}
            </label>
        </div>
    @endforeach

    @include('filament::fields.array-fields.error-help')
</div>
