<x-filament-actions::action
    :action="$action"
    component="filament-support::dropdown.list.item"
    :icon="$action->getGroupedIcon()"
    class="filament-actions-grouped-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
