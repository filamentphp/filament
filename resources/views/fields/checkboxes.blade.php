<fieldset class="mb-5">
    <legend class="text-sm font-medium leading-5 mb-1">
        {{ $field->label }}
    </legend>
    @foreach ($field->options as $value => $label)
        <label class="flex items-center mb-1">
            <input
                type="checkbox"
                class="form-checkbox dark:bg-gray-900 dark:border-gray-700 h-4 w-4 transition duration-150 ease-in-out 
                    @error($field->key) 
                        text-red-500 
                    @else 
                        text-blue-500 
                    @enderror
                "
                value="{{ $value }}"
                wire:model.lazy="{{ $field->key }}.{{ $loop->index }}"
            >
            <span class="ml-2 text-sm leading-5">{{ __($label) }}</span>
        </label>
    @endforeach
    @include('filament::fields.error-help')
</fieldset>
