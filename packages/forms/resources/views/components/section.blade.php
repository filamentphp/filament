<div
    @if ($isCollapsible())
        x-data="{ isCollapsed: {{ $isCollapsed() ? 'true' : 'false' }} }"
        x-on:open-form-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:collapse-form-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:toggle-form-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
        x-on:expand-concealing-component.window="
            if ($event.detail.id === $el.id) {
                isCollapsed = false

                setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 100)
            }
        "
    @endif
    id="{{ $getId() }}"
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-forms-section-component bg-white rounded-xl border border-gray-300',
        'dark:border-gray-600 dark:bg-gray-800' => config('forms.dark_mode'),
    ]) }}
    {{ $getExtraAlpineAttributeBag() }}
>
    <div
        @class([
            'filament-forms-section-header-wrapper flex items-center px-4 py-2 bg-gray-100 rtl:space-x-reverse overflow-hidden rounded-t-xl min-h-[40px]',
            'min-h-[56px]' => ! $isCompact(),
            'dark:bg-gray-900' => config('forms.dark_mode'),
        ])
        @if ($isCollapsible())
            x-bind:class="{ 'rounded-b-xl': isCollapsed }"
            x-on:click="
                isCollapsed = ! isCollapsed

                if (isCollapsed) {
                    return
                }

                setTimeout(
                    () => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }),
                    100,
                )
            "
        @endif
    >
        <div @class([
            'filament-forms-section-header flex-1',
            'cursor-pointer' => $isCollapsible(),
        ])>
            <h3 @class([
                'font-bold tracking-tight pointer-events-none',
                'text-xl font-bold'=> ! $isCompact(),
            ])>
                {{ $getHeading() }}
            </h3>

            @if ($description = $getDescription())
                <p class="text-gray-500 pointer-events-none">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($isCollapsible())
            <button x-on:click.stop="isCollapsed = ! isCollapsed"
                x-bind:class="{
                    '-rotate-180': !isCollapsed,
                }" type="button"
                @class([
                    'flex items-center justify-center transform rounded-full text-primary-500 hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none',
                    'w-10 h-10' => ! $isCompact(),
                    'w-8 h-8 -my-1' => $isCompact(),
                    '-rotate-180' => ! $isCollapsed(),
                ])
            >
                <svg @class([
                    'w-7 h-7' => ! $isCompact(),
                    'w-5 h-5' => $isCompact(),
                ]) xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        @endif
    </div>

    <div
        @if ($isCollapsible())
            x-bind:class="{ 'invisible h-0 !m-0 overflow-y-hidden': isCollapsed }"
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($isCollapsed()) x-cloak @endif
        @endif
        class="filament-forms-section-content-wrapper"
    >
        <div @class([
            'filament-forms-section-content',
            'p-6' => ! $isCompact(),
            'p-4' => $isCompact(),
        ])>
            {{ $getChildComponentContainer() }}
        </div>
    </div>
</div>
