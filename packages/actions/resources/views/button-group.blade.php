<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="filament-actions-button-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
