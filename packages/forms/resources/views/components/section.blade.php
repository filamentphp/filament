@php
    $isAside = $isAside();
    $isCollapsed = $isCollapsed();
    $isCollapsible = $isCollapsible() && (! $isAside);
    $isCompact = $isCompact();
@endphp

<div
    @if ($isCollapsible)
        x-data="{
            isCollapsed: @js($isCollapsed),
        }"
        x-on:open-form-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:collapse-form-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:toggle-form-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
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
    id="{{ $getId() }}"
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-forms-section-component',
        'rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10' => ! $isAside,
        'grid grid-cols-1 md:grid-cols-2' => $isAside,
    ]) }}
    {{ $getExtraAlpineAttributeBag() }}
>
    <div
        @class([
            'filament-forms-section-header-wrapper flex rtl:space-x-reverse overflow-hidden rounded-t-xl',
            'min-h-[40px]' => $isCompact,
            'min-h-[56px]' => ! $isCompact,
            'pr-6 pb-4' => $isAside,
            'px-4 py-2 items-center bg-gray-100 dark:bg-gray-900' => ! $isAside,
        ])
        @if ($isCollapsible)
            x-bind:class="{ 'rounded-b-xl': isCollapsed }"
            x-on:click="isCollapsed = ! isCollapsed"
        @endif
    >
        <div @class([
            'filament-forms-section-header flex-1 space-y-1',
            'cursor-pointer' => $isCollapsible,
        ])>
            <h3 @class([
                'font-bold tracking-tight pointer-events-none',
                'text-xl font-bold'=> ! $isCompact,
            ])>
                {{ $getHeading() }}
            </h3>

            @if ($description = $getDescription())
                <p class="text-gray-500 text-base pointer-events-none">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($isCollapsible)
            <button
                x-on:click.stop="isCollapsed = ! isCollapsed"
                x-bind:class="{
                    '-rotate-180': !isCollapsed,
                }"
                type="button"
                @class([
                    'flex items-center justify-center transform rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
                    'w-10 h-10' => ! $isCompact,
                    'w-8 h-8 -my-1' => $isCompact,
                    '-rotate-180' => ! $isCollapsed,
                ])
            >
                <x-filament::icon
                    name="heroicon-m-chevron-down"
                    alias="filament-forms::section.buttons.collapse"
                    color="text-primary-500"
                    :size="$isCompact ? 'h-5 w-5' : 'h-7 w-7'"
                />
            </button>
        @endif
    </div>

    <div
        @if ($isCollapsible)
            x-bind:class="{ 'invisible h-0 !m-0 overflow-y-hidden': isCollapsed }"
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($isCollapsed) x-cloak @endif
        @endif
        class="filament-forms-section-content-wrapper"
    >
        <div @class([
            'filament-forms-section-content',
            'rounded-xl bg-white ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10' => $isAside,
            'p-6' => ! $isCompact,
            'p-4' => $isCompact,
        ])>
            {{ $getChildComponentContainer() }}
        </div>
    </div>
</div>
