<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $containers = $getChildComponentContainers();

        $isAddable = $isAddable();
        $isCollapsible = $isCollapsible();
        $isCloneable = $isCloneable();
        $isDeletable = $isDeletable();
        $isReorderable = $isReorderable();
        $hasItemLabels = $hasItemLabels();

        $statePath = $getStatePath();
    @endphp

    <div>
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="space-x-2 rtl:space-x-reverse" x-data="{}">
                <x-filament::link
                    x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('filament-forms::components.repeater.buttons.collapse_all.label') }}
                </x-filament::link>

                <x-filament::link
                    x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('filament-forms::components.repeater.buttons.expand_all.label') }}
                </x-filament::link>
            </div>
        @endif
    </div>

    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'filament-forms-repeater-component space-y-6 rounded-xl',
                    'bg-gray-50 p-6 dark:bg-gray-500/10' => $isInset(),
                ])
        }}
    >
        @if (count($containers))
            <ul>
                <x-filament::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    x-sortable
                    x-on:end.stop="$wire.dispatchFormEvent('repeater::reorder', '{{ $statePath }}', $event.target.sortable.toArray())"
                    class="gap-6"
                >
                    @foreach ($containers as $uuid => $item)
                        <li
                            x-data="{
                                isCollapsed: @js($isCollapsed()),
                            }"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            wire:key="{{ $this->id }}.{{ $item->getStatePath() }}.item"
                            x-sortable-item="{{ $uuid }}"
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
                            class="relative rounded-xl bg-white shadow-sm ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10"
                        >
                            @if ($isReorderable || $isDeletable || $isCloneable || $isCollapsible || $hasItemLabels)
                                <header
                                    @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                    @class([
                                        'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl dark:bg-gray-800 dark:border-gray-700',
                                        'cursor-pointer' => $isCollapsible,
                                    ])
                                >
                                    @if ($isReorderable)
                                        <button
                                            title="{{ __('filament-forms::components.repeater.buttons.reorder.label') }}"
                                            x-on:click.stop
                                            x-sortable-handle
                                            wire:keydown.prevent.arrow-up="dispatchFormEvent('repeater::moveUp', '{{ $statePath }}', '{{ $uuid }}')"
                                            wire:keydown.prevent.arrow-down="dispatchFormEvent('repeater::moveDown', '{{ $statePath }}', '{{ $uuid }}')"
                                            type="button"
                                            class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 border-r transition hover:text-gray-500 dark:border-gray-700"
                                        >
                                            <span class="sr-only">
                                                {{ __('filament-forms::components.repeater.buttons.reorder.label') }}
                                            </span>

                                            <x-filament::icon
                                                name="heroicon-m-arrows-up-down"
                                                alias="filament-forms::components.repeater.buttons.reorder"
                                                size="h-4 w-4"
                                            />
                                        </button>
                                    @endif

                                    <p class="flex-none px-4 text-xs font-medium text-gray-600 truncate dark:text-gray-400">
                                        {{ $getItemLabel($uuid) }}
                                    </p>

                                    <div class="flex-1"></div>

                                    <ul class="flex divide-x rtl:divide-x-reverse dark:divide-gray-700">
                                        @if ($isCloneable)
                                            <li>
                                                <button
                                                    title="{{ __('filament-forms::components.repeater.buttons.clone.label') }}"
                                                    wire:click.stop="dispatchFormEvent('repeater::cloneItem', '{{ $statePath }}', '{{ $uuid }}')"
                                                    type="button"
                                                    class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500 dark:border-gray-700"
                                                >
                                                    <span class="sr-only">
                                                        {{ __('filament-forms::components.repeater.buttons.clone.label') }}
                                                    </span>

                                                    <x-filament::icon
                                                        name="heroicon-m-square-2-stack"
                                                        alias="filament-forms::components.repeater.buttons.clone"
                                                        size="h-4 w-4"
                                                    />
                                                </button>
                                            </li>
                                        @endif

                                        @if ($isDeletable)
                                            <li>
                                                <button
                                                    title="{{ __('filament-forms::components.repeater.buttons.delete.label') }}"
                                                    wire:click.stop="dispatchFormEvent('repeater::delete', '{{ $statePath }}', '{{ $uuid }}')"
                                                    type="button"
                                                    class="flex items-center justify-center flex-none w-10 h-10 text-danger-600 transition hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                                >
                                                    <span class="sr-only">
                                                        {{ __('filament-forms::components.repeater.buttons.delete.label') }}
                                                    </span>

                                                    <x-filament::icon
                                                        name="heroicon-m-trash"
                                                        alias="filament-forms::components.repeater.buttons.delete"
                                                        size="h-4 w-4"
                                                    />
                                                </button>
                                            </li>
                                        @endif

                                        @if ($isCollapsible)
                                            <li>
                                                <button
                                                    x-bind:title="(! isCollapsed) ? '{{ __('filament-forms::components.repeater.buttons.collapse.label') }}' : '{{ __('filament-forms::components.repeater.buttons.expand.label') }}'"
                                                    x-on:click.stop="isCollapsed = ! isCollapsed"
                                                    type="button"
                                                    class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500"
                                                >
                                                    <x-filament::icon
                                                        name="heroicon-m-minus"
                                                        alias="filament-forms::components.repeater.buttons.collapse"
                                                        size="h-4 w-4"
                                                        x-show="!isCollapsed"
                                                    />

                                                    <span class="sr-only" x-show="! isCollapsed">
                                                        {{ __('filament-forms::components.repeater.buttons.collapse.label') }}
                                                    </span>

                                                    <x-filament::icon
                                                        name="heroicon-m-plus"
                                                        alias="filament-forms::components.repeater.buttons.expand"
                                                        size="h-4 w-4"
                                                        x-show="isCollapsed"
                                                        x-cloak=""
                                                    />

                                                    <span class="sr-only" x-show="isCollapsed" x-cloak>
                                                        {{ __('filament-forms::components.repeater.buttons.expand.label') }}
                                                    </span>
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </header>
                            @endif

                            <div class="p-6" x-show="! isCollapsed">
                                {{ $item }}
                            </div>

                            <div class="p-2 text-xs text-center text-gray-400" x-show="isCollapsed" x-cloak>
                                {{ __('filament-forms::components.repeater.collapsed') }}
                            </div>
                        </li>
                    @endforeach
                </x-filament::grid>
            </ul>
        @endif

        @if ($isAddable)
            <div class="relative flex justify-center">
                <x-filament::button
                    :wire:click="'dispatchFormEvent(\'repeater::add\', \'' . $statePath . '\')'"
                    size="sm"
                    type="button"
                >
                    {{ $getAddButtonLabel() }}
                </x-filament::button>
            </div>
        @endif
    </div>
</x-dynamic-component>
