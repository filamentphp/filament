<x-filament-tables::actions.action
    :action="$action"
    component="filament-support::dropdown.list.item"
    :icon="$action->getGroupedIcon()"
    class="filament-tables-grouped-action"
>
    {{ $getLabel() }}
</x-filament-tables::actions.action>
