@props([
    'actions',
])

<x-filament::actions
    :actions="$actions"
    :align="config('filament.layout.forms.actions.alignment')"
/>
