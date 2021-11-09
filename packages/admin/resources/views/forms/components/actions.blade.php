@if (count($actions = $getActions()))
    <x-filament::actions :actions="$actions" />
@endif
