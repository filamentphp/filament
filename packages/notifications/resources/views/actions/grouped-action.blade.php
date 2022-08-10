<x-notifications::actions.action
    :action="$action"
    component="notifications::dropdown.item"
    :icon-position="$getIconPosition()"
    class="filament-notifications-grouped-action"
>
    {{ $getLabel() }}
</x-notifications::actions.action>
