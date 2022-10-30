@props([
    'actions',
    'alignment' => null,
    'fullWidth' => false,
])

<x-filament-actions::actions
    :actions="$actions"
    :alignment="$alignment ?? static::getFormActionsAlignment()"
    :full-width="$fullWidth"
    class="filament-form-actions"
/>
