@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'aside' => false,
    'collapsed' => false,
    'collapsible' => false,
    'compact' => false,
    'contentBefore' => false,
    'description' => null,
    'headerEnd' => null,
    'heading' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconSize' => IconSize::Large,
])

@php
    $hasDescription = filled((string) $description);
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);
    $hasHeader = $hasIcon || $hasHeading || $hasDescription || $collapsible || filled((string) $headerEnd);
@endphp

<section
    {{-- TODO: Investigate Livewire bug - https://github.com/filamentphp/filament/pull/8511 --}}
    x-data="{
        isCollapsed: @js($collapsed),
    }"
    @if ($collapsible)
        x-on:collapse-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:expand-concealing-component.window="
            $nextTick(() => {
                error = $el.querySelector('[data-validation-error]')

                if (! error) {
                    return
                }

                isCollapsed = false

                if (document.body.querySelector('[data-validation-error]') !== error) {
                    return
                }

                setTimeout(
                    () =>
                        $el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'start',
                        }),
                    200,
                )
            })
        "
        x-on:open-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:toggle-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
        x-bind:class="isCollapsed && 'fi-collapsed'"
    @endif
    {{
        $attributes->class([
            'fi-section',
            match ($aside) {
                true => 'fi-aside grid grid-cols-1 items-start gap-x-6 gap-y-4 md:grid-cols-3',
                false => 'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            },
        ])
    }}
>
    @if ($hasHeader)
        <header
            @if ($collapsible)
                x-on:click="isCollapsed = ! isCollapsed"
            @endif
            @class([
                'fi-section-header flex items-center gap-x-3 overflow-hidden',
                'cursor-pointer' => $collapsible,
                match ($compact) {
                    true => 'px-4 py-2.5',
                    false => 'px-6 py-4',
                } => ! $aside,
            ])
        >
            @if ($hasIcon)
                <x-filament::icon
                    :icon="$icon"
                    @class([
                        'fi-section-header-icon self-start',
                        match ($iconColor) {
                            'gray' => 'fi-color-gray text-gray-400 dark:text-gray-500',
                            default => 'fi-color-custom text-custom-500 dark:text-custom-400',
                        },
                        match ($iconSize) {
                            IconSize::Small, 'sm' => 'h-4 w-4 mt-1',
                            IconSize::Medium, 'md' => 'h-5 w-5 mt-0.5',
                            IconSize::Large, 'lg' => 'h-6 w-6',
                            default => $iconSize,
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables(
                            $iconColor,
                            shades: [400, 500],
                        ) => $iconColor !== 'gray',
                    ])
                />
            @endif

            @if ($hasHeading || $hasDescription)
                <div class="grid flex-1 gap-y-1">
                    @if ($hasHeading)
                        <x-filament::section.heading>
                            {{ $heading }}
                        </x-filament::section.heading>
                    @endif

                    @if ($hasDescription)
                        <x-filament::section.description>
                            {{ $description }}
                        </x-filament::section.description>
                    @endif
                </div>
            @endif

            {{ $headerEnd }}

            @if ($collapsible)
                <x-filament::icon-button
                    color="gray"
                    icon="heroicon-m-chevron-down"
                    icon-alias="section.collapse-button"
                    x-on:click.stop="isCollapsed = ! isCollapsed"
                    x-bind:class="{ 'rotate-180': ! isCollapsed }"
                />
            @endif
        </header>
    @endif

    <div
        @if ($collapsible)
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($collapsed)
                x-cloak
            @endif
            x-bind:class="{ 'invisible h-0 overflow-y-hidden border-none': isCollapsed }"
        @endif
        @class([
            'fi-section-content-ctn',
            'md:col-span-2' => $aside,
            'border-t border-gray-200 dark:border-white/10' => $hasHeader && (! $aside),
            'md:order-first' => $contentBefore,
        ])
    >
        <div
            @class([
                'fi-section-content',
                'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => $aside,
                match ($compact) {
                    true => 'p-4',
                    false => 'p-6',
                },
            ])
        >
            {{ $slot }}
        </div>
    </div>
</section>
