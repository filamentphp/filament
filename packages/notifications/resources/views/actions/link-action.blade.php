<x-notifications::actions.action
    :action="$action"
    component="notifications::link"
    :icon-position="$getIconPosition()"
    class="filament-notifications-link-action"
>
    {{ $getLabel() }}
</x-notifications::actions.action>
