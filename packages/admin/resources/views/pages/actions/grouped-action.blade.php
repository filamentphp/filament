<x-filament::pages.actions.action
    :action="$action"
    component="filament::dropdown.item"
    class="filament-grouped-action"
>
    {{ $getLabel() }}
</x-filament::pages.actions.action>
