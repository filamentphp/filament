<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::link"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="filament-actions-link-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
