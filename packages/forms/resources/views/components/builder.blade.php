<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $containers = $getChildComponentContainers();

        $addAction = $getAction($getAddActionName());
        $addBetweenAction = $getAction($getAddBetweenActionName());
        $cloneAction = $getAction($getCloneActionName());
        $deleteAction = $getAction($getDeleteActionName());
        $moveDownAction = $getAction($getMoveDownActionName());
        $moveUpAction = $getAction($getMoveUpActionName());
        $reorderAction = $getAction($getReorderActionName());

        $isCollapsible = $isCollapsible();
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
                    {{ __('filament-forms::components.builder.actions.collapse_all.label') }}
                </x-filament::link>

                <x-filament::link
                    x-on:click="$dispatch('builder-expand', '{{ $statePath }}')"
                    tag="button"
                    size="sm"
                >
                    {{ __('filament-forms::components.builder.actions.expand_all.label') }}
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
                :wire:end.stop="'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })'"
                @class([
                    'space-y-12' => $addAction && $reorderAction,
                    'space-y-6' => ! ($addAction && $reorderAction),
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
                            isCollapsed: @js($isCollapsed($item)),
                        }"
                        x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                        x-on:builder-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:click="isAddButtonVisible = true"
                        x-on:mouseenter="isAddButtonVisible = true"
                        x-on:click.away="isAddButtonVisible = false"
                        x-on:mouseleave="isAddButtonVisible = false"
                        wire:key="{{ $this->id }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
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
                        @if ($reorderAction || $hasBlockLabels || $deleteAction || $isCollapsible || $isCloneable)
                            <header
                                @if ($isCollapsible) x-on:click.stop="isCollapsed = ! isCollapsed" @endif
                                @class([
                                    'flex items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl dark:bg-gray-800 dark:border-gray-700',
                                    'cursor-pointer' => $isCollapsible,
                                ])
                            >
                                @if ($reorderAction)
                                    <div
                                        x-on:click.stop
                                        x-sortable-handle
                                    >
                                        {{ $reorderAction }}
                                    </div>
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
                                        @if (! $loop->first)
                                            <li class="flex items-center justify-center">
                                                {{ $moveUpAction(['item' => $uuid]) }}
                                            </li>
                                        @endif

                                        @if (! $loop->last)
                                            <li class="flex items-center justify-center">
                                                {{ $moveDownAction(['item' => $uuid]) }}
                                            </li>
                                        @endif
                                    @endif

                                    @if ($cloneAction)
                                        <li class="flex items-center justify-center">
                                            {{ $cloneAction(['item' => $uuid]) }}
                                        </li>
                                    @endif

                                    @if ($deleteAction)
                                        <li class="flex items-center justify-center">
                                            {{ $deleteAction(['item' => $uuid]) }}
                                        </li>
                                    @endif

                                    @if ($isCollapsible)
                                        <li>
                                            <button
                                                x-bind:title="(! isCollapsed) ? '{{ __('filament-forms::components.builder.actions.collapse.label') }}' : '{{ __('filament-forms::components.builder.actions.expand.label') }}'"
                                                x-on:click.stop="isCollapsed = ! isCollapsed"
                                                type="button"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-gray-400 transition hover:text-gray-500"
                                            >
                                                <x-filament::icon
                                                    name="heroicon-m-minus"
                                                    alias="filament-forms::components.builder.actions.collapse"
                                                    size="h-4 w-4"
                                                    x-show="!isCollapsed"
                                                />

                                                <span class="sr-only" x-show="! isCollapsed">
                                                    {{ __('filament-forms::components.builder.actions.collapse.label') }}
                                                </span>

                                                <x-filament::icon
                                                    name="heroicon-m-plus"
                                                    alias="filament-forms::components.builder.actions.expand"
                                                    size="h-4 w-4"
                                                    x-show="isCollapsed"
                                                    x-cloak="x-cloak"
                                                />

                                                <span class="sr-only" x-show="isCollapsed" x-cloak>
                                                    {{ __('filament-forms::components.builder.actions.expand.label') }}
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

                        @if ((! $loop->last) && $addBetweenAction && $reorderAction)
                            <div
                                x-show="isAddButtonVisible"
                                x-transition
                                class="absolute inset-x-0 bottom-0 flex items-center justify-center h-12 -mb-12"
                            >
                                <x-filament-forms::builder.block-picker
                                    :action="$addBetweenAction"
                                    :blocks="$getBlocks()"
                                    :after-item="$uuid"
                                    :state-path="$statePath"
                                >
                                    <x-slot name="trigger">
                                        {{ $addBetweenAction }}
                                    </x-slot>
                                </x-filament-forms::builder.block-picker>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($addAction)
            <x-filament-forms::builder.block-picker
                :action="$addAction"
                :blocks="$getBlocks()"
                :state-path="$statePath"
                class="flex justify-center"
            >
                <x-slot name="trigger">
                    {{ $addAction }}
                </x-slot>
            </x-filament-forms::builder.block-picker>
        @endif
    </div>
</x-dynamic-component>
