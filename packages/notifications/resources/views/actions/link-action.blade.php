<x-filament-notifications::actions.action
    :action="$action"
    component="filament-notifications::link"
    :icon-position="$getIconPosition()"
    class="filament-notifications-link-action"
>
    {{ $getLabel() }}
</x-filament-notifications::actions.action>
