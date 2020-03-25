<div class="mb-6">
    @if ($field->label)
        <label for="{{ $field->name }}" class="block mb-2 text-sm font-medium leading-5 text-gray-700">
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">(required)</span>
            @endif
        </label>
    @endif
    <div class="relative mb-2">
        <input
            id="{{ $field->name }}"
            type="{{ $field->input_type }}"
            class="form-input block w-full sm:text-sm sm:leading-5 
                @error($field->key)
                    pr-10 border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red
                @enderror"
            autocomplete="{{ $field->autocomplete }}"
            placeholder="{{ $field->placeholder }}"
            wire:model.lazy="{{ $field->key }}"
        >
        @error($field->key) 
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                {{ Filament::svg('heroicons/solid-sm/sm-exclamation-circle', 'h-5 w-5 text-red-500') }}
            </div>
        @enderror
    </div>
    @include('filament::fields.error-help')
</div>
