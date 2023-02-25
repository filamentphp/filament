<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::link"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="filament-actions-link-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
