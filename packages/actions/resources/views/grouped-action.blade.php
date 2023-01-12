<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::dropdown.list.item"
    :icon="$getGroupedIcon()"
    class="filament-actions-grouped-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
