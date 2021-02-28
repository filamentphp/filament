@props([
'columns' => 1,
'schema' => [],
])

@php
    $columnsClasses = [
        '',
        'grid-cols-1 lg:grid-cols-1',
        'grid-cols-1 lg:grid-cols-2',
        'grid-cols-1 lg:grid-cols-3',
        'grid-cols-1 lg:grid-cols-4',
        'grid-cols-1 lg:grid-cols-5',
        'grid-cols-1 lg:grid-cols-6',
        'grid-cols-1 lg:grid-cols-7',
        'grid-cols-1 lg:grid-cols-8',
        'grid-cols-1 lg:grid-cols-9',
        'grid-cols-1 lg:grid-cols-10',
        'grid-cols-1 lg:grid-cols-11',
        'grid-cols-1 lg:grid-cols-12',
    ][$columns]
@endphp

@if (count($schema))
    <div {{ $attributes }}>
        <div class="grid {{ $columnsClasses }} gap-6">
            @foreach ($schema as $component)
                {{ $component->render() }}
            @endforeach
        </div>
    </div>
@endif
