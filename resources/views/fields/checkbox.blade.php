<div class="form-group row">
    <div class="col-md-2 col-form-label text-md-right py-md-0">
        {{ $field->placeholder ? $field->label : '' }}
    </div>

    <div class="col-md">
        <div class="form-check">
            <input
                id="{{ $field->name }}"
                type="checkbox"
                class="form-check-input @error($field->key) is-invalid @enderror"
                wire:model.lazy="{{ $field->key }}">

            <label class="form-check-label" for="{{ $field->name }}">
                {{ $field->placeholder ?? $field->label }}
            </label>
        </div>

        @include('filament::fields.error-help')
    </div>
</div>
