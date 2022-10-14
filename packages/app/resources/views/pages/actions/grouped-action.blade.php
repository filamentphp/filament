<x-filament::pages.actions.action
    :action="$action"
    component="filament::dropdown.list.item"
    :icon="$action->getGroupedIcon()"
    class="filament-grouped-action"
>
    {{ $getLabel() }}
</x-filament::pages.actions.action>
