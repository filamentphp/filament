@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\IconSize;

    use function Filament\Support\is_slot_empty;
@endphp

@props([
    'afterHeader' => null,
    'aside' => false,
    'collapsed' => false,
    'collapsible' => false,
    'compact' => false,
    'contentBefore' => false,
    'description' => null,
    'footer' => null,
    'heading' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconSize' => IconSize::Large,
    'persistCollapsed' => false,
])

@php
    $hasDescription = filled((string) $description);
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);
    $hasHeader = $hasIcon || $hasHeading || $hasDescription || $collapsible || (! is_slot_empty($afterHeader));
@endphp

<section
    {{-- TODO: Investigate Livewire bug - https://github.com/filamentphp/filament/pull/8511 --}}
    x-data="{
        isCollapsed: @if ($persistCollapsed) $persist(@js($collapsed)).as(`section-${$el.id}-isCollapsed`) @else @js($collapsed) @endif,
    }"
    @if ($collapsible)
        x-on:collapse-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:expand="isCollapsed = false"
        x-on:open-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:toggle-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
        x-bind:class="isCollapsed && 'fi-collapsed'"
    @endif
    {{
        $attributes->class([
            'fi-section',
            'fi-section-has-content-before' => filled($contentBefore),
            'fi-section-has-header' => $hasHeader,
            'fi-aside' => $aside,
            'fi-compact' => $compact,
            'fi-collapsible' => $collapsible,
        ])
    }}
>
    @if ($hasHeader)
        <header
            @if ($collapsible)
                x-on:click="isCollapsed = ! isCollapsed"
            @endif
            class="fi-section-header"
        >
            {{
                \Filament\Support\generate_icon_html($icon, attributes: (new \Illuminate\View\ComponentAttributeBag)
                    ->class([
                        'fi-section-header-icon',
                        match ($iconColor) {
                            'gray' => null,
                            default => 'fi-color-custom',
                        },
                        is_string($iconColor) ? "fi-color-{$iconColor}" : null,
                        ($iconSize instanceof IconSize) ? "fi-size-{$iconSize->value}" : (is_string($iconSize) ? $iconSize : null),
                    ])
                    ->style([
                        \Filament\Support\get_color_css_variables(
                            $iconColor,
                            shades: [400, 500],
                            alias: 'section.header.icon',
                        ) => $iconColor !== 'gray',
                    ]))
            }}

            @if ($hasHeading || $hasDescription)
                <div class="fi-section-header-text-ctn">
                    @if ($hasHeading)
                        <h3 class="fi-section-header-heading">
                            {{ $heading }}
                        </h3>
                    @endif

                    @if ($hasDescription)
                        <p class="fi-section-header-description">
                            {{ $description }}
                        </p>
                    @endif
                </div>
            @endif

            {{ $afterHeader }}

            @if ($collapsible)
                <x-filament::icon-button
                    color="gray"
                    icon="heroicon-m-chevron-down"
                    icon-alias="section.collapse-button"
                    x-on:click.stop="isCollapsed = ! isCollapsed"
                    class="fi-section-collapse-btn"
                />
            @endif
        </header>
    @endif

    <div
        @if ($collapsible)
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($collapsed || $persistCollapsed)
                x-cloak
            @endif
        @endif
        class="fi-section-content-ctn"
    >
        <div class="fi-section-content">
            {{ $slot }}
        </div>

        @if (! is_slot_empty($footer))
            <footer class="fi-section-footer">
                {{ $footer }}
            </footer>
        @endif
    </div>
</section>
