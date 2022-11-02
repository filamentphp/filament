<x-tables::actions.action
    :action="$action"
    component="tables::dropdown.list.item"
    :icon="$action->getGroupedIcon()"
    class="filament-tables-grouped-action"
>
    {{ $getLabel() }}
</x-tables::actions.action>
