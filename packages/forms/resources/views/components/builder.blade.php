<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $containers = $getChildComponentContainers();
        $blockPickerColumns = $getBlockPickerColumns();
        $blockPickerWidth = $getBlockPickerWidth();
        $blocks = $getBlocks();

        $addAction = $getAction($getAddActionName());
        $addBetweenAction = $getAction($getAddBetweenActionName());
        $cloneAction = $getAction($getCloneActionName());
        $collapseAllAction = $getAction($getCollapseAllActionName());
        $expandAllAction = $getAction($getExpandAllActionName());
        $deleteAction = $getAction($getDeleteActionName());
        $moveDownAction = $getAction($getMoveDownActionName());
        $moveUpAction = $getAction($getMoveUpActionName());
        $reorderAction = $getAction($getReorderActionName());

        $isAddable = $isAddable();
        $isCloneable = $isCloneable();
        $isCollapsible = $isCollapsible();
        $isDeletable = $isDeletable();
        $isReorderableWithButtons = $isReorderableWithButtons();
        $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

        $statePath = $getStatePath();
    @endphp

    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-builder grid gap-y-4'])
        }}
    >
        @if ($isCollapsible && ($collapseAllAction->isVisible() || $expandAllAction->isVisible()))
            <div
                @class([
                    'flex gap-x-3',
                    'hidden' => count($containers) < 2,
                ])
            >
                @if ($collapseAllAction->isVisible())
                    <span
                        x-on:click="$dispatch('builder-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllAction->isVisible())
                    <span
                        x-on:click="$dispatch('builder-expand', '{{ $statePath }}')"
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if (count($containers))
            <ul
                x-sortable
                wire:end.stop="{{ 'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })' }}"
                class="space-y-4"
            >
                @php
                    $hasBlockLabels = $hasBlockLabels();
                    $hasBlockNumbers = $hasBlockNumbers();
                @endphp

                @foreach ($containers as $uuid => $item)
                    <li
                        wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                        x-data="{
                            isCollapsed: @js($isCollapsed($item)),
                        }"
                        x-on:builder-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
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
                        x-sortable-item="{{ $uuid }}"
                        class="fi-fo-builder-item rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
                        x-bind:class="{ 'fi-collapsed overflow-hidden': isCollapsed }"
                    >
                        @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || $hasBlockLabels || $isCloneable || $isDeletable || $isCollapsible)
                            <div
                                class="fi-fo-builder-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3"
                            >
                                @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons)
                                    <ul class="flex items-center gap-x-3">
                                        @if ($isReorderableWithDragAndDrop)
                                            <li x-sortable-handle>
                                                {{ $reorderAction }}
                                            </li>
                                        @endif

                                        @if ($isReorderableWithButtons)
                                            <li>
                                                {{ $moveUpAction(['item' => $uuid])->disabled($loop->first) }}
                                            </li>

                                            <li>
                                                {{ $moveDownAction(['item' => $uuid])->disabled($loop->last) }}
                                            </li>
                                        @endif
                                    </ul>
                                @endif

                                @if ($hasBlockLabels)
                                    <h4
                                        @if ($isCollapsible)
                                            x-on:click.stop="isCollapsed = !isCollapsed"
                                        @endif
                                        @class([
                                            'text-sm font-medium text-gray-950 dark:text-white',
                                            'truncate' => $isBlockLabelTruncated(),
                                            'cursor-pointer select-none' => $isCollapsible,
                                        ])
                                    >
                                        {{ $item->getParentComponent()->getLabel($item->getRawState(), $uuid) }}

                                        @if ($hasBlockNumbers)
                                            {{ $loop->iteration }}
                                        @endif
                                    </h4>
                                @endif

                                @if ($isCloneable || $isDeletable || $isCollapsible)
                                    <ul
                                        class="ms-auto flex items-center gap-x-3"
                                    >
                                        @if ($isCloneable)
                                            <li>
                                                {{ $cloneAction(['item' => $uuid]) }}
                                            </li>
                                        @endif

                                        @if ($isDeletable)
                                            <li>
                                                {{ $deleteAction(['item' => $uuid]) }}
                                            </li>
                                        @endif

                                        @if ($isCollapsible)
                                            <li
                                                class="relative transition"
                                                x-on:click.stop="isCollapsed = !isCollapsed"
                                                x-bind:class="{ '-rotate-180': isCollapsed }"
                                            >
                                                <div
                                                    class="transition"
                                                    x-bind:class="{ 'opacity-0 pointer-events-none': isCollapsed }"
                                                >
                                                    {{ $getAction('collapse') }}
                                                </div>

                                                <div
                                                    class="absolute inset-0 rotate-180 transition"
                                                    x-bind:class="{ 'opacity-0 pointer-events-none': ! isCollapsed }"
                                                >
                                                    {{ $getAction('expand') }}
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        @endif

                        <div
                            x-show="! isCollapsed"
                            class="fi-fo-builder-item-content border-t border-gray-100 p-4 dark:border-white/10"
                        >
                            {{ $item }}
                        </div>
                    </li>

                    @if (! $loop->last)
                        @if ($isAddable && $addBetweenAction->isVisible())
                            <li class="relative -top-2 !mt-0 h-0">
                                <div
                                    class="flex w-full justify-center opacity-0 transition duration-75 hover:opacity-100"
                                >
                                    <div
                                        class="fi-fo-builder-block-picker-ctn rounded-lg bg-white dark:bg-gray-900"
                                    >
                                        <x-filament-forms::builder.block-picker
                                            :action="$addBetweenAction"
                                            :after-item="$uuid"
                                            :columns="$blockPickerColumns"
                                            :blocks="$blocks"
                                            :state-path="$statePath"
                                            :width="$blockPickerWidth"
                                        >
                                            <x-slot name="trigger">
                                                {{ $addBetweenAction }}
                                            </x-slot>
                                        </x-filament-forms::builder.block-picker>
                                    </div>
                                </div>
                            </li>
                        @elseif (filled($labelBetweenItems = $getLabelBetweenItems()))
                            <li
                                class="relative border-t border-gray-200 dark:border-white/10"
                            >
                                <span
                                    class="absolute -top-3 left-3 bg-white px-1 text-sm font-medium dark:bg-gray-900"
                                >
                                    {{ $labelBetweenItems }}
                                </span>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable)
            <x-filament-forms::builder.block-picker
                :action="$addAction"
                :blocks="$blocks"
                :columns="$blockPickerColumns"
                :state-path="$statePath"
                :width="$blockPickerWidth"
                class="flex justify-center"
            >
                <x-slot name="trigger">
                    {{ $addAction }}
                </x-slot>
            </x-filament-forms::builder.block-picker>
        @endif
    </div>
</x-dynamic-component>
