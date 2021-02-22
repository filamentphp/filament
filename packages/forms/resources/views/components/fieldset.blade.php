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
    ][$formComponent->columnSpan];
@endphp

<fieldset class="{{ $columnSpanClass }} {{ $formComponent->label ? 'rounded border border-gray-200 p-4 md:px-6' : null }}">
    @if ($formComponent->label)
        <legend class="text-sm leading-tight font-medium px-2">
            {{ __($formComponent->label) }}
        </legend>
    @endif

    <x-forms::section :schema="$formComponent->schema" :columns="$formComponent->columns" />
</fieldset>
