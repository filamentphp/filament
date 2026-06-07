@props([
    'columnSpan' => [],
    'columnStart' => [],
    'height' => null,
])

<div
    {{
        ($attributes ?? new \Illuminate\View\ComponentAttributeBag)
            ->gridColumn($columnSpan, $columnStart)
            ->class(['fi-section fi-loading-section'])
            ->style(['height: ' . e($height ?? '8rem')])
    }}
></div>
