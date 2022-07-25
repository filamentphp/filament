<x-notifications::actions.action
    :action="$action"
    component="notifications::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-notifications-button-action"
>
    {{ $getLabel() }}
</x-notifications::actions.action>
