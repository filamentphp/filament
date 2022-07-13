<x-notifications::actions.action
    :action="$action"
    component="notifications::link"
    class="filament-notifications-link-action"
>
    {{ $getLabel() }}
</x-notifications::actions.action>
