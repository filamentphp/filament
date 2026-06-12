@php
    use Filament\Support\Enums\VerticalAlignment;

    $actions = $getChildSchema()->getComponents();
    $alignment = $getAlignment();
    $isFullWidth = $isFullWidth();
    $isSticky = $isSticky();
    $verticalAlignment = $getVerticalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }
@endphp

<div
    @if ($isSticky)
        x-data="filamentActionsSchemaComponent()"
        x-intersect:enter.half="disableSticky"
        x-intersect:leave="enableSticky"
        x-bind:class="{ 'fi-sticky': isSticky }"
    @endif
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-actions',
                ($verticalAlignment instanceof VerticalAlignment) ? "fi-vertical-align-{$verticalAlignment->value}" : $verticalAlignment,
            ])
    }}
>
    @if (filled($label = $getLabel()))
        <div class="fi-sc-actions-label-ctn">
            {{ $getChildSchema($schemaComponent::BEFORE_LABEL_SCHEMA_KEY) }}

            <div class="fi-sc-actions-label">
                {{ $label }}
            </div>

            {{ $getChildSchema($schemaComponent::AFTER_LABEL_SCHEMA_KEY) }}
        </div>
    @endif

    @if ($aboveContentContainer = $getChildSchema($schemaComponent::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString())
        {{ $aboveContentContainer }}
    @endif

    <x-filament::actions
        :actions="$actions"
        :alignment="$alignment"
        :full-width="$isFullWidth"
        :x-bind:style="$isSticky ? 'isSticky ? `width: ${width}px;` : \'\'' : null"
    />

    @if ($belowContentContainer = $getChildSchema($schemaComponent::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString())
        {{ $belowContentContainer }}
    @endif
</div>
