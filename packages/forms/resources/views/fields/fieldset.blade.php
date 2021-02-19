@php
    $columnSpanClass = [
        '',
        'lg:col-span-1',
        'lg:col-span-2',
        'lg:col-span-3',
        'lg:col-span-4',
        'lg:col-span-5',
        'lg:col-span-6',
        'lg:col-span-7',
        'lg:col-span-8',
        'lg:col-span-9',
        'lg:col-span-10',
        'lg:col-span-11',
        'lg:col-span-12',
    ][$field->columnSpan];
@endphp

<fieldset class="{{ $columnSpanClass }} {{ $field->label ? 'rounded border border-gray-200 p-4 md:px-6' : null }}">
    @if ($field->label)
        <legend class="text-sm leading-tight font-medium px-2">
            {{ __($field->label) }}
        </legend>
    @endif

    <x-forms::section :fields="$field->fields" :columns="$field->columns" />
</fieldset>
