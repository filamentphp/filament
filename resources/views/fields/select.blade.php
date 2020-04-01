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
        <select
            id="input-{{ $field->name }}"
            wire:model.lazy="{{ $field->key }}">
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
