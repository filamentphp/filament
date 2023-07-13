<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::dropdown.list.item"
    :icon="$getGroupedIcon()"
    class="fi-ac-grouped-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
