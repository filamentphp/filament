<x-notifications::actions.action
    :action="$action"
    component="notifications::button"
    :outlined="$isOutlined()"
    class="filament-notifications-button-action"
>
    {{ $getLabel() }}
</x-notifications::actions.action>
