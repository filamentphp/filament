@php
    $columnSpan = $this->getColumnSpan();

    if (! is_array($columnSpan)) {
        $columnSpan = [
            'default' => $columnSpan,
        ];
    }

    $columnStart = $this->getColumnStart();

    if (! is_array($columnStart)) {
        $columnStart = [
            'default' => $columnStart,
        ];
    }
@endphp

<x-filament::grid.column
    :default="$columnSpan['default'] ?? 1"
    :sm="$columnSpan['sm'] ?? null"
    :md="$columnSpan['md'] ?? null"
    :lg="$columnSpan['lg'] ?? null"
    :xl="$columnSpan['xl'] ?? null"
    :twoXl="$columnSpan['2xl'] ?? null"
    :defaultStart="$columnStart['default'] ?? null"
    :smStart="$columnStart['sm'] ?? null"
    :mdStart="$columnStart['md'] ?? null"
    :lgStart="$columnStart['lg'] ?? null"
    :xlStart="$columnStart['xl'] ?? null"
    :twoXlStart="$columnStart['2xl'] ?? null"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->class('fi-wi-widget')"
>
    {{ $slot }}
</x-filament::grid.column>
