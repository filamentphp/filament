<x-filament-actions::group
    :group="$group"
    dynamic-component="filament::badge"
    :icon-position="$getIconPosition()"
    class="fi-ac-badge-group"
>
    {{ $getLabel() }}
</x-filament-actions::group>
