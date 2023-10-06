<x-filament-actions::group
    dynamic-component="filament::link"
    :group="$group"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    :size="$getSize()"
    tag="button"
    class="fi-ac-link-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
