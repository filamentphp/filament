<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::link"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="fi-ac-link-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
