<div class="mb-5">
    @if ($field->label)
        <label for="input-{{ $field->name }}" class="block mb-2 text-sm font-medium leading-5 text-gray-700 dark:text-gray-50">
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">(required)</span>
            @endif
        </label>
    @endif
    <div class="relative mb-2">
        <textarea
            id="input-{{ $field->name }}"
            rows="{{ $field->textarea_rows }}"
            class="form-input dark:text-gray-50 dark:bg-gray-900 dark:border-gray-700 block w-full sm:text-sm sm:leading-5 
                @error($field->key)
                    pr-10 border-red-300 dark:border-red-500 text-red-900 placeholder-red-500 dark:placeholder-red-500 focus:border-red-500 dark-focus:border-red-500 focus:shadow-outline-red dark-focus:shadow-outline-red
                @enderror"            
            placeholder="{{ $field->placeholder }}"
            @if ($field->value)
                value="{{ $field->value }}"
            @endif
            wire:model.lazy="{{ $field->key }}"></textarea>
        @error($field->key) 
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-600" />
            </div>
        @enderror
    </div>
    @include('filament::fields.error-help')
</div>
