<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $containers = $getChildComponentContainers();

        $isAddable = $isAddable();
        $isCloneable = $isCloneable();
        $isCollapsible = $isCollapsible();
        $isDeletable = $isDeletable();
        $isReorderable = $isReorderable();
        $isReorderableWithButtons = $isReorderableWithButtons();

        $statePath = $getStatePath();
    @endphp

    <div>
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="space-x-2 rtl:space-x-reverse" x-data="{}">
                <x-filament::link
                    x-on:click="$dispatch('builder-collapse', '{{ $statePath }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('filament-forms::components.builder.buttons.collapse_all.label') }}
                </x-filament::link>

                <x-filament::link
                    x-on:click="$dispatch('builder-expand', '{{ $statePath }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('filament-forms::components.builder.buttons.expand_all.label') }}
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
                    'filament-forms-builder-component space-y-6 rounded-xl',
                    'bg-gray-50 p-6 dark:bg-gray-500/10' => $isInset(),
                ])
        }}
    >
        @if (count($containers))
            <ul
                x-sortable
                x-on:end.stop="$wire.dispatchFormEvent('builder::reorder', '{{ $statePath }}', $event.target.sortable.toArray())"
                @class([
                    'space-y-12' => $isAddable && $isReorderable,
                    'space-y-6' => ! ($isAddable && $isReorderable),
                ])
            >
                @php
                    $hasBlockLabels = $hasBlockLabels();
                    $hasBlockNumbers = $hasBlockNumbers();
                @endphp

                @foreach ($containers as $uuid => $item)
                    <li
                        x-data="{
                            isAddButtonVisible: false,
                            isCollapsed: @js($isCollapsed()),
                        }"
                        x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                        x-on:builder-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:click="isAddButtonVisible = true"
                        x-on:mouseenter="isAddButtonVisible = true"
                        x-on:click.away="isAddButtonVisible = false"
                        x-on:mouseleave="isAddButtonVisible = false"
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
                        @if ($isReorderable || $hasBlockLabels || $isDeletable || $isCollapsible || $isCloneable)
                            <header
                                @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                @class([
                                    'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl dark:bg-gray-800 dark:border-gray-700',
                                    'cursor-pointer' => $isCollapsible,
                                ])
                            >
                                @if ($isReorderable)
                                    <button
                                        title="{{ __('filament-forms::components.builder.buttons.reorder.label') }}"
                                        x-on:click.stop
                                        x-sortable-handle
                                        wire:keydown.prevent.arrow-up="dispatchFormEvent('builder::moveUp', '{{ $statePath }}', '{{ $uuid }}')"
                                        wire:keydown.prevent.arrow-down="dispatchFormEvent('builder::moveDown', '{{ $statePath }}', '{{ $uuid }}')"
                                        type="button"
                                        class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 border-r rtl:border-l rtl:border-r-0 transition hover:text-gray-500 dark:border-gray-700"
                                    >
                                        <span class="sr-only">
                                            {{ __('filament-forms::components.builder.buttons.reorder.label') }}
                                        </span>

                                        <x-filament::icon
                                            name="heroicon-m-arrows-up-down"
                                            alias="filament-forms::components.builder.buttons.reorder"
                                            size="h-4 w-4"
                                        />
                                    </button>
                                @endif

                                @if ($hasBlockLabels)
                                    <p class="flex-none px-4 text-xs font-medium text-gray-600 truncate dark:text-gray-400">
                                        @php
                                            $block = $item->getParentComponent();

                                            $block->labelState($item->getRawState());
                                        @endphp

                                        {{ $item->getParentComponent()->getLabel() }}

                                        @php
                                            $block->labelState(null);
                                        @endphp

                                        @if ($hasBlockNumbers)
                                            <small class="font-mono">{{ $loop->iteration }}</small>
                                        @endif
                                    </p>
                                @endif

                                <div class="flex-1"></div>

                                <ul class="flex divide-x rtl:divide-x-reverse dark:divide-gray-700">
                                    @if ($isReorderableWithButtons)
                                        @unless ($loop->first)
                                            <li>
                                                <button
                                                    title="{{ __('filament-forms::components.builder.buttons.move_item_up.label') }}"
                                                    wire:click.stop="dispatchFormEvent('builder::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    type="button"
                                                    class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500 dark:border-gray-700"
                                                >
                                                    <span class="sr-only">
                                                        {{ __('filament-forms::components.builder.buttons.move_item_up.label') }}
                                                    </span>

                                                    <x-filament::icon
                                                        name="heroicon-m-chevron-up"
                                                        alias="filament-forms::components.builder.buttons.move_item_up"
                                                        size="h-4 w-4"
                                                    />
                                                </button>
                                            </li>
                                        @endunless

                                        @unless ($loop->last)
                                            <li>
                                                <button
                                                    title="{{ __('filament-forms::components.builder.buttons.move_item_down.label') }}"
                                                    wire:click.stop="dispatchFormEvent('builder::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                                    type="button"
                                                    class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500 dark:border-gray-700"
                                                >
                                                    <span class="sr-only">
                                                        {{ __('filament-forms::components.builder.buttons.move_item_down.label') }}
                                                    </span>

                                                    <x-filament::icon
                                                        name="heroicon-m-chevron-down"
                                                        alias="filament-forms::components.builder.buttons.move_item_down"
                                                        size="h-4 w-4"
                                                    />
                                                </button>
                                            </li>
                                        @endunless
                                    @endif

                                    @if ($isCloneable)
                                        <li>
                                            <button
                                                title="{{ __('filament-forms::components.builder.buttons.clone.label') }}"
                                                wire:click.stop="dispatchFormEvent('builder::cloneItem', '{{ $statePath }}', '{{ $uuid }}')"
                                                type="button"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500 dark:border-gray-700"
                                            >
                                                <span class="sr-only">
                                                    {{ __('filament-forms::components.builder.buttons.clone.label') }}
                                                </span>

                                                <x-filament::icon
                                                    name="heroicon-m-square-2-stack"
                                                    alias="filament-forms::components.builder.buttons.clone"
                                                    size="h-4 w-4"
                                                />
                                            </button>
                                        </li>
                                    @endif

                                    @if ($isDeletable)
                                        <li>
                                            <button
                                                title="{{ __('filament-forms::components.builder.buttons.delete.label') }}"
                                                wire:click.stop="dispatchFormEvent('builder::delete', '{{ $statePath }}', '{{ $uuid }}')"
                                                type="button"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-danger-600 transition hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                            >
                                                <span class="sr-only">
                                                    {{ __('filament-forms::components.builder.buttons.delete.label') }}
                                                </span>

                                                <x-filament::icon
                                                    name="heroicon-m-trash"
                                                    alias="filament-forms::components.builder.buttons.delete"
                                                    size="h-4 w-4"
                                                />
                                            </button>
                                        </li>
                                    @endif

                                    @if ($isCollapsible)
                                        <li>
                                            <button
                                                x-bind:title="(! isCollapsed) ? '{{ __('filament-forms::components.builder.buttons.collapse.label') }}' : '{{ __('filament-forms::components.builder.buttons.expand.label') }}'"
                                                x-on:click.stop="isCollapsed = ! isCollapsed"
                                                type="button"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500"
                                            >
                                                <x-filament::icon
                                                    name="heroicon-m-minus"
                                                    alias="filament-forms::components.builder.buttons.collapse"
                                                    size="h-4 w-4"
                                                    x-show="!isCollapsed"
                                                />

                                                <span class="sr-only" x-show="! isCollapsed">
                                                    {{ __('filament-forms::components.builder.buttons.collapse.label') }}
                                                </span>

                                                <x-filament::icon
                                                    name="heroicon-m-plus"
                                                    alias="filament-forms::components.builder.buttons.expand"
                                                    size="h-4 w-4"
                                                    x-show="isCollapsed"
                                                    x-cloak="x-cloak"
                                                />

                                                <span class="sr-only" x-show="isCollapsed" x-cloak>
                                                    {{ __('filament-forms::components.builder.buttons.expand.label') }}
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
                            {{ __('filament-forms::components.builder.collapsed') }}
                        </div>

                        @if ((! $loop->last) && $isAddable && $isReorderable)
                            <div
                                x-show="isAddButtonVisible"
                                x-transition
                                class="absolute inset-x-0 bottom-0 flex items-center justify-center h-12 -mb-12"
                            >
                                <x-filament-forms::builder.block-picker
                                    :blocks="$getBlocks()"
                                    :after-item="$uuid"
                                    :state-path="$statePath"
                                >
                                    <x-slot name="trigger">
                                        <x-filament::icon-button
                                            :label="$getAddBetweenButtonLabel()"
                                            icon="heroicon-m-plus"
                                            icon-alias="forms::builder.add-between.trigger"
                                        />
                                    </x-slot>
                                </x-filament-forms::builder.block-picker>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($isAddable)
            <x-filament-forms::builder.block-picker
                :blocks="$getBlocks()"
                :state-path="$statePath"
                class="flex justify-center"
            >
                <x-slot name="trigger">
                    <x-filament::button size="sm">
                        {{ $getAddButtonLabel() }}
                    </x-filament::button>
                </x-slot>
            </x-filament-forms::builder.block-picker>
        @endif
    </div>
</x-dynamic-component>
