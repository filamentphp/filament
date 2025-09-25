@php
    $description = $getDescription();
    $footer = $getChildSchema($schemaComponent::FOOTER_SCHEMA_KEY)?->toHtmlString();
    $heading = $getHeading();
    $headingTag = $getHeadingTag();
    $icon = $getIcon();
    $iconColor = $getIconColor();
    $iconSize = $getIconSize();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-sc-empty-state'])
    }}
>
    <x-filament::empty-state
        :description="$description"
        :footer="$footer"
        :heading="$heading"
        :heading-tag="$headingTag"
        :icon="$icon"
        :icon-color="$iconColor"
        :icon-size="$iconSize"
    />
</div>
