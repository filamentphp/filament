<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::link"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="fi-ac-link-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
