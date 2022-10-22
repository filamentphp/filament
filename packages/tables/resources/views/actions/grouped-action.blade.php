<x-filament-tables::actions.action
    :action="$action"
    component="filament::dropdown.list.item"
    :icon="$action->getGroupedIcon()"
    class="filament-tables-grouped-action"
>
    {{ $getLabel() }}
</x-filament-tables::actions.action>
