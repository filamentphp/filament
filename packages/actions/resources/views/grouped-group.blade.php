<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::dropdown.list.item"
    :icon="$getGroupedIcon()"
    class="filament-actions-grouped-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
