<x-filament-tables::actions.action
    :action="$action"
    component="filament::link"
    :icon-position="$getIconPosition()"
    class="filament-tables-link-action"
>
    {{ $getLabel() }}
</x-filament-tables::actions.action>
