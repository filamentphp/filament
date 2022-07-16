@props([
    'actions',
    'fullWidth' => false,
])

<x-filament::pages.actions
    :actions="$actions"
    :align="config('filament.layout.forms.actions.alignment')"
    :full-width="$fullWidth"
    class="filament-form-actions"
/>
