@props([
    'actions',
    'color' => null,
    'dropdownPlacement' => null,
    'icon' => null,
    'label' => null,
    'size' => null,
    'tooltip' => null,
    'view' => null,
])

{{
    \Filament\Actions\ActionGroup::make($actions)
        ->color($color)
        ->dropdownPlacement($dropdownPlacement)
        ->icon($icon)
        ->label($label)
        ->size($size)
        ->tooltip($tooltip)
        ->view($view)
}}