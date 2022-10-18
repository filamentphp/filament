<x-filament-actions::.action
    :action="$action"
    component="filament-actions::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-page-button-action"
>
    {{ $getLabel() }}
</x-filament-actions::.action>
