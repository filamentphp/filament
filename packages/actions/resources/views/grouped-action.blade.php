<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::dropdown.list.item"
    :icon="$getGroupedIcon()"
    class="fi-ac-grouped-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
