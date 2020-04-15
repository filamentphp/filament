<div class="col-span-4 {{ $field->class }}">
    @if ($field->label)
        <label 
            for="{{ $field->id }}" 
            class="
                label 
                @if ($field->disabled) 
                    label-disabled
                @endif
                block mb-2
            "
        >
            {{ __($field->label) }}
            @if ($field->required)
                <sup class="text-red-600">*</sup>
                <span class="sr-only">{{ __('required') }}</span>
            @endif
        </label>
    @endif
    <div class="relative mb-2">
        <textarea
            id="{{ $field->id }}"
            name="{{ $field->name }}"
            class="form-input input w-full
                @error($field->key)
                    input-error
                @enderror"            
            rows="{{ $field->textarea_rows }}"
            placeholder="{{ $field->placeholder }}"
            @if ($field->value)
                value="{{ $field->value }}"
            @endif
            wire:model.lazy="{{ $field->key }}"
            @if ($field->disabled) 
                disabled
            @endif
        ></textarea>
        @error($field->key) 
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-600" />
            </div>
        @enderror
    </div>
    @include('filament::fields.error-help')
</div>
