@php
$classes = $column->getValue($record) ? 'text-primary-600' : 'text-danger-700';
@endphp

@include('tables::cells.icon')
