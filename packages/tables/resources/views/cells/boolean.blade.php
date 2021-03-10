@include('tables::cells.icon', [
    'classes' => $column->getValue($record) ? 'text-primary-600' : 'text-danger-700',
])
