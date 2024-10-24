@props([
    'action',
    'dynamicComponent',
    'icon' => null,
])

@php
    $isDisabled = $action->isDisabled();
    $url = $action->getUrl();
    $tooltip = $action->getTooltip();
    $tooltipIsNotEmpty = str($tooltip)->isNotEmpty();
    $icon = $icon ?? $action->getIcon();
@endphp

@if ($tooltipIsNotEmpty) <div x-data x-tooltip.raw="{{ $tooltip }}" @class(['flex', 'items-center justify-center -m-2 h-9 w-9' => str($icon)->isNotEmpty() && $action->isIconButton()])> @endif
    <x-dynamic-component
        :color="$action->getColor()"
        :component="$dynamicComponent"
        :disabled="$isDisabled"
        :form="$action->getFormToSubmit()"
        :form-id="$action->getFormId()"
        :href="$isDisabled ? null : $url"
        :icon="$icon"
        :icon-size="$action->getIconSize()"
        :key-bindings="$action->getKeyBindings()"
        :label-sr-only="$action->isLabelHidden()"
        :tag="$url ? 'a' : 'button'"
        :target="($url && $action->shouldOpenUrlInNewTab()) ? '_blank' : null"
        :type="$action->canSubmitForm() ? 'submit' : 'button'"
        :wire:click="$action->getLivewireClickHandler()"
        :wire:target="$action->getLivewireTarget()"
        :x-on:click="$action->getAlpineClickHandler()"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($action->getExtraAttributes(), escape: false)
                ->class(['fi-ac-action', 'flex-1' => $tooltipIsNotEmpty])
        "
    >
        {{ $slot }}
    </x-dynamic-component>
@if ($tooltipIsNotEmpty) </div> @endif
