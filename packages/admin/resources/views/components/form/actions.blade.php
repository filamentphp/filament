@props([
    'actions',
])

<x-filament::pages.actions
    :actions="$actions"
    :align="config('filament.layout.forms.actions.alignment')"
    class="filament-form-actions"
/>
