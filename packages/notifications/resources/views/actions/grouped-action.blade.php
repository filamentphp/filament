<x-filament-notifications::actions.action
    :action="$action"
    component="filament-notifications::dropdown.list.item"
    :icon-position="$getIconPosition()"
    class="filament-notifications-grouped-action"
>
    {{ $getLabel() }}
</x-filament-notifications::actions.action>
