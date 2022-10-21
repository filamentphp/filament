<x-filament-notifications::actions.action
    :action="$action"
    component="filament-support::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-notifications-button-action"
>
    {{ $getLabel() }}
</x-filament-notifications::actions.action>
