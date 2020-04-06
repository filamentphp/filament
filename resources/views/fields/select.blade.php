<div class="col-span-4 {{ $field->class }}">
    @if ($field->label)
        <label 
            for="input-{{ $field->name }}" 
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
                <span class="sr-only">(required)</span>
            @endif
        </label>
    @endif
    <div class="relative mb-2">
        <select
            id="input-{{ $field->name }}"
            wire:model.lazy="{{ $field->key }}"
            @if ($field->disabled) 
                disabled
            @endif
        >
            @if ($field->placeholder)
                <option value="">{{ $field->placeholder }}</option>
            @endif
            @foreach ($field->options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @include('filament::fields.error-help')
</div>
