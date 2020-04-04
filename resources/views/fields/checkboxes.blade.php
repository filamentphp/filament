<fieldset class="col-span-4 {{ $field->class }}">
    <legend class="block mb-2 text-sm font-medium leading-5 text-gray-700 dark:text-gray-50">
        {{ $field->label }}
        @if ($field->required)
            <sup class="text-red-600">*</sup>
            <span class="sr-only">(required)</span>
        @endif
    </legend>
    <ol class="list-unstyled">
        @foreach ($field->options as $value => $label)
            <li class="mb-1">
                <x-filament-checkbox :type="$type ?? 'checkbox'" :name="$field->key" :label="$label" :value="$value" :model="$field->key" :disabled="$field->disabled" />
            </li>
        @endforeach
    </ol>
    @include('filament::fields.error-help')
</fieldset>
