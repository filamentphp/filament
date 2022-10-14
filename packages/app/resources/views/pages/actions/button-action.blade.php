<x-filament::pages.actions.action
    :action="$action"
    component="filament::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-page-button-action"
>
    {{ $getLabel() }}
</x-filament::pages.actions.action>
