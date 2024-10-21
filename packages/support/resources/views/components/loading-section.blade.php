@props([
    'columnSpan' => [],
    'columnStart' => [],
    'height' => null,
])

<x-filament::section
    :attributes="
        \Filament\Support\prepare_inherited_attributes(
            ($attributes ?? new \Illuminate\View\ComponentAttributeBag)
                ->gridColumn($columnSpan, $columnStart)
                ->class(['fi-loading-section'])
                ->style(['height: ' . ($height ?? '8rem')])
        )
    "
/>
