<fieldset class="col-span-4 {{ $field->class }}">
    <legend class="label w-full mb-2">
        {{ $field->label }}
        @if ($field->required)
            <sup class="text-red-600">*</sup>
            <span class="sr-only">{{ __('required') }}</span>
        @endif
    </legend>
    <ol class="list-unstyled">
        @foreach ($field->options as $value => $label)
            <li class="mb-1">
                <x-filament-checkbox :type="$type ?? 'checkbox'" :name="$field->name" :label="$label" :value="$value" :model="$field->key" :disabled="$field->disabled" />
            </li>
        @endforeach
    </ol>
    @include('filament::fields.error-help')
</fieldset>
