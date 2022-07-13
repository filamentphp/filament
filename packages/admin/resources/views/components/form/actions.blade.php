@props([
    'actions',
    'fullWidth' => false,
])

<x-filament::pages.actions
    :actions="$actions"
    :full-width="$fullWidth"
    class="filament-form-actions"
/>
