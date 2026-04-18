@php
    use Filament\Support\Enums\IconSize;

    $controls = $getChildSchema($schemaComponent::CONTROLS_SCHEMA_KEY)?->toHtmlString();
    $extraAttributeBag = $getExtraAttributeBag();
    $footer = $getChildSchema($schemaComponent::FOOTER_SCHEMA_KEY)?->toHtmlString();
    $color = $getColor();
    $description = $getDescription();
    $heading = $getHeading();
    $icon = $getIcon();
    $iconColor = $getIconColor();
    $iconSize = $getIconSize() ?? IconSize::Large;
@endphp

<x-filament::callout
    :attributes="
        \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
            ->class(['fi-sc-callout'])
    "
    :color="$color ?? 'gray'"
    :description="$description"
    :heading="$heading"
    :icon="$icon"
    :icon-color="$iconColor"
    :icon-size="$iconSize"
>
    <x-slot name="footer">
        {{ $footer }}
    </x-slot>

    <x-slot name="controls">
        {{ $controls }}
    </x-slot>
</x-filament::callout>
