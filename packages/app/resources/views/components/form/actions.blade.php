@props([
    'actions',
    'alignment' => 'left',
    'fullWidth' => false,
])

<x-filament-actions::actions
    :actions="$actions"
    :alignment="$alignment"
    :full-width="$fullWidth"
    class="filament-form-actions"
/>
