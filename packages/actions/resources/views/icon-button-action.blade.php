<x-filament-actions::action
    :action="$action"
    :label="$getLabel()"
    :inline="$isInline()"
    dynamic-component="filament::icon-button"
    class="filament-actions-icon-button-action"
/>
