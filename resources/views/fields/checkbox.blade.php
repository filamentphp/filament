<div class="mb-5">
    <label class="inline-flex items-center">
        <input
            type="checkbox"
            class="form-checkbox h-4 w-4 transition duration-150 ease-in-out 
                @error($field->key) 
                    text-red-500 
                @else 
                    text-blue-500 
                @enderror
            "
            @if ($field->value)
                value="{{ $field->value }}"
            @endif
            wire:model.lazy="{{ $field->key }}"
        >
        <span class="ml-2 text-sm leading-5 text-gray-700">{{ __($field->placeholder ?? $field->label) }}</span>
    </label>
    @include('filament::fields.error-help')
</div>
