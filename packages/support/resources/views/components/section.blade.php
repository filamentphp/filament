@props([
    'aside' => false,
    'collapsed' => false,
    'collapsible' => false,
    'compact' => false,
    'contentBefore' => false,
    'description' => null,
    'heading',
    'icon' => null,
])

<section
    @if ($collapsible)
        x-data="{
            isCollapsed: @js($collapsed),
        }"
        x-on:open-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:collapse-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:toggle-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
        x-on:expand-concealing-component.window="
            error = $el.querySelector('[data-validation-error]')

            if (! error) {
                return
            }

            isCollapsed = false

            if (document.body.querySelector('[data-validation-error]') !== error) {
                return
            }

            setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 200)
        "
    @endif
    {{ $attributes->class([
        'filament-section-component',
        'rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10' => ! $aside,
        'grid grid-cols-1' => $aside,
        'md:grid-cols-2' => $aside && ! $compact,
        'md:grid-cols-3' => $aside && $compact,
        'md:order-last' => $contentBefore,
    ]) }}
>
    <div
        @class([
            'filament-section-header-wrapper flex rtl:space-x-reverse overflow-hidden rounded-t-xl',
            'min-h-[40px]' => $compact,
            'min-h-[56px]' => ! $compact,
            'pb-4' => $aside,
            'pe-6' => $aside && ! $contentBefore,
            'ps-6' => $aside && $contentBefore,
            'px-4 py-2 items-center bg-gray-100 dark:bg-gray-900' => ! $aside,
        ])
        @if ($collapsible)
            x-bind:class="{ 'rounded-b-xl': isCollapsed }"
            x-on:click="isCollapsed = ! isCollapsed"
        @endif
    >
        <div @class([
            'filament-section-header flex-1 space-y-1',
            'cursor-pointer' => $collapsible,
        ])>
            <h3 @class([
                'font-medium leading-6 pointer-events-none flex flex-row items-center',
                'text-lg'=> ! $compact || $aside,
            ])>
                @if ($icon)
                    <x-dynamic-component
                        :component="$icon"
                        @class([
                            'me-1',
                            'h-4 w-4' => $compact && ! $aside,
                            'h-6 w-6' => ! $compact || $aside,
                        ])
                    />
                @endif

                {{ $heading }}
            </h3>

            @if ($description?->isNotEmpty())
                <p @class([
                    'text-gray-500',
                    'text-sm' => $compact && ! $aside,
                    'text-base' => ! $compact || $aside,
                ])>
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($collapsible)
            <button
                x-on:click.stop="isCollapsed = ! isCollapsed"
                x-bind:class="{
                    '-rotate-180': !isCollapsed,
                }"
                type="button"
                @class([
                    'flex items-center justify-center transform rounded-full outline-none hover:bg-gray-500/5 focus:bg-primary-500/10',
                    'w-10 h-10' => ! $compact,
                    'w-8 h-8 -my-1' => $compact,
                    '-rotate-180' => ! $collapsed,
                ])
            >
                <x-filament::icon
                    name="heroicon-m-chevron-down"
                    alias="support::section.buttons.collapse"
                    color="text-primary-500"
                    :size="$compact ? 'h-5 w-5' : 'h-7 w-7'"
                />
            </button>
        @endif
    </div>

    <div
        @if ($collapsible)
            x-bind:class="{ 'invisible h-0 !m-0 overflow-y-hidden': isCollapsed }"
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($collapsed) x-cloak @endif
        @endif
        @class([
             'filament-section-content-wrapper',
             'col-span-2' => $aside && $compact,
             'md:order-first' => $contentBefore,
         ])
    >
        <div @class([
            'filament-section-content',
            'rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10' => $aside,
            'p-6' => ! $compact || $aside,
            'p-4' => $compact && ! $aside,
        ])>
            {{ $slot }}
        </div>
    </div>
</section>
