<x-tables::actions.action
    :action="$action"
    component="tables::dropdown.item"
    class="filament-tables-grouped-action"
>
    {{ $getLabel() }}
</x-tables::actions.action>
