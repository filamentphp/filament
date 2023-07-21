@props([
    'aside' => false,
    'collapsed' => false,
    'collapsible' => false,
    'compact' => false,
    'contentBefore' => false,
    'description' => null,
    'heading',
    'icon' => null,
    'iconColor' => 'gray',
    'iconSize' => 'lg',
])

<section
    @if ($collapsible)
        x-data="{
            isCollapsed: @js($collapsed),
        }"
        x-on:collapse-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:expand-concealing-component.window="
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
        "
        x-on:open-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:toggle-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
    @endif
    {{
        $attributes->class([
            'fi-section',
            match ($aside) {
                true => 'grid grid-cols-1 items-start gap-x-6 gap-y-4 md:grid-cols-3',
                false => 'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            },
        ])
    }}
>
    <div
        @if ($collapsible)
            x-on:click="isCollapsed = ! isCollapsed"
        @endif
        @class([
            'fi-section-header-ctn flex items-center overflow-hidden',
            'cursor-pointer' => $collapsible,
            match ($compact) {
                true => 'px-4 py-2.5',
                false => 'px-6 py-4',
            } => ! $aside,
        ])
    >
        <div class="fi-section-header flex gap-x-3">
            @if ($icon)
                <x-filament::icon
                    :icon="$icon"
                    @class([
                        'fi-section-header-icon',
                        match ($iconColor) {
                            'gray' => 'text-gray-400 dark:text-gray-500',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                        match ($iconSize) {
                            'sm' => 'h-4 w-4 mt-1',
                            'md' => 'h-5 w-5 mt-0.5',
                            'lg' => 'h-6 w-6',
                            default => $iconSize,
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables($iconColor, shades: [400, 500]) => $iconColor !== 'gray',
                    ])
                />
            @endif

            <div class="flex-1">
                <h3
                    class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white"
                >
                    {{ $heading }}
                </h3>

                @if (filled((string) $description))
                    <p
                        class="fi-section-header-description mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        @if ($collapsible)
            <x-filament::icon-button
                color="gray"
                icon="heroicon-m-chevron-down"
                icon-alias="section.collapse-button"
                x-on:click.stop="isCollapsed = ! isCollapsed"
                x-bind:class="{ 'rotate-180': ! isCollapsed }"
                class="-my-2.5 -me-2.5 ms-auto"
            />
        @endif
    </div>

    <div
        @if ($collapsible)
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($collapsed)
                x-cloak
            @endif
            x-bind:class="{ 'invisible h-0 border-none': isCollapsed }"
        @endif
        @class([
            'fi-section-content-ctn',
            'md:col-span-2' => $aside,
            'border-t border-gray-100 dark:border-white/10' => ! $aside,
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
