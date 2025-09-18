@php
    $afterHeader = $getChildSchema($schemaComponent::AFTER_HEADER_SCHEMA_KEY)?->toHtmlString();
    $isAside = $isAside();
    $isCollapsed = $isCollapsed();
    $isCollapsible = $isCollapsible();
    $isCompact = $isCompact();
    $isContained = $isContained();
    $isDivided = $isDivided();
    $isFormBefore = $isFormBefore();
    $description = $getDescription();
    $footer = $getChildSchema($schemaComponent::FOOTER_SCHEMA_KEY)?->toHtmlString();
    $heading = $getHeading();
    $headingTag = $getHeadingTag();
    $icon = $getIcon();
    $iconColor = $getIconColor();
    $iconSize = $getIconSize();
    $shouldPersistCollapsed = $shouldPersistCollapsed();
    $isSecondary = $isSecondary();
    $id = $getId();
@endphp

<div
    {{
        $attributes
            ->merge([
                'id' => $id,
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['fi-sc-section'])
    }}
>
    @if (filled($label = $getLabel()))
        <div class="fi-sc-section-label-ctn">
            {{ $getChildSchema($schemaComponent::BEFORE_LABEL_SCHEMA_KEY) }}

            <div class="fi-sc-section-label">
                {{ $label }}
            </div>

            {{ $getChildSchema($schemaComponent::AFTER_LABEL_SCHEMA_KEY) }}
        </div>
    @endif

    @if ($aboveContentContainer = $getChildSchema($schemaComponent::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString())
        {{ $aboveContentContainer }}
    @endif

    <x-filament::section
        :after-header="$afterHeader"
        :aside="$isAside"
        :collapsed="$isCollapsed"
        :collapse-id="$id"
        :collapsible="$isCollapsible && (! $isAside)"
        :compact="$isCompact"
        :contained="$isContained"
        :content-before="$isFormBefore"
        :description="$description"
        :divided="$isDivided"
        :footer="$footer"
        :has-content-el="false"
        :heading="$heading"
        :heading-tag="$headingTag"
        :icon="$icon"
        :icon-color="$iconColor"
        :icon-size="$iconSize"
        :persist-collapsed="$shouldPersistCollapsed"
        :secondary="$isSecondary"
    >
        {{ $getChildSchema()->gap(! $isDivided)->extraAttributes(['class' => 'fi-section-content']) }}
    </x-filament::section>

    @if ($belowContentContainer = $getChildSchema($schemaComponent::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString())
        {{ $belowContentContainer }}
    @endif
</div>
