<x-filament-actions::group
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::dropdown.list.item"
    :group="$group"
    :icon="$getGroupedIcon()"
    class="fi-ac-grouped-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
