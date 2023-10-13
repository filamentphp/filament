<x-filament-actions::group
    dynamic-component="filament::badge"
    :group="$group"
    :icon-position="$getIconPosition()"
    :size="$getSize()"
    class="fi-ac-badge-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
