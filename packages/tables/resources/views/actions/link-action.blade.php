<x-filament-tables::actions.action
    :action="$action"
    component="filament-tables::link"
    :icon-position="$getIconPosition()"
    class="filament-tables-link-action"
>
    {{ $getLabel() }}
</x-filament-tables::actions.action>
