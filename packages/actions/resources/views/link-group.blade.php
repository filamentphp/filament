<x-filament-actions::group
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::link"
    :group="$group"
    :icon-position="$getIconPosition()"
    :size="$getSize()"
    tag="button"
    class="fi-ac-link-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
