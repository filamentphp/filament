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

<x-forms::tabs class="{{ $columnSpanClass }}" :id="$field->id" :label="__($field->label)" :tabs="$field->getTabsConfig()">
    @foreach($field->fields as $tab)
        {{ $tab->render() }}
    @endforeach
</x-forms::tabs>
