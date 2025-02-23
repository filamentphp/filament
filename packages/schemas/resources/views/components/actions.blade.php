@php
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Facades\FilamentView;

    $actions = $getChildSchema()->getComponents();
    $alignment = $getAlignment();
    $isFullWidth = $isFullWidth();
    $verticalAlignment = $getVerticalAlignment();

    if (! $verticalAlignment instanceof VerticalAlignment) {
        $verticalAlignment = filled($verticalAlignment) ? (VerticalAlignment::tryFrom($verticalAlignment) ?? $verticalAlignment) : null;
    }
@endphp

<div
    @if ($isSticky())
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}x-load="visible || event (x-modal-opened)"{{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('actions', 'filament/schemas') }}"
        x-data="actionsSchemaComponent()"
        x-on:scroll.window="evaluatePageScrollPosition"
        x-bind:class="{
            'fi-sticky': isSticky,
        }"
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
    />

    @if ($belowContentContainer = $getChildSchema($schemaComponent::BELOW_CONTENT_SCHEMA_KEY)?->toHtmlString())
        {{ $belowContentContainer }}
    @endif
</div>
