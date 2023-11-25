<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $containers = $getChildComponentContainers();

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
                ->class(['fi-fo-repeater grid gap-y-4'])
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
                        x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllAction->isVisible())
                    <span
                        x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')"
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if (count($containers))
            <ul>
                <x-filament::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :two-xl="$getGridColumns('2xl')"
                    :wire:end.stop="'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })'"
                    x-sortable
                    class="gap-4"
                >
                    @foreach ($containers as $uuid => $item)
                        @php
                            $itemLabel = $getItemLabel($uuid);
                        @endphp

                        <li
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                            }"
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
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-sortable-item="{{ $uuid }}"
                            class="fi-fo-repeater-item rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
                            x-bind:class="{ 'fi-collapsed overflow-hidden': isCollapsed }"
                        >
                            @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || filled($itemLabel) || $isCloneable || $isDeletable || $isCollapsible)
                                <div
                                    class="fi-fo-repeater-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3"
                                >
                                    @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons)
                                        <ul class="flex items-center gap-x-3">
                                            @if ($isReorderableWithDragAndDrop)
                                                <li x-sortable-handle>
                                                    {{ $reorderAction }}
                                                </li>
                                            @endif

                                            @if ($isReorderableWithButtons)
                                                <li
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveUpAction(['item' => $uuid])->disabled($loop->first) }}
                                                </li>

                                                <li
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveDownAction(['item' => $uuid])->disabled($loop->last) }}
                                                </li>
                                            @endif
                                        </ul>
                                    @endif

                                    @if (filled($itemLabel))
                                        <h4
                                            @if ($isCollapsible)
                                                x-on:click.stop="isCollapsed = !isCollapsed"
                                            @endif
                                            @class([
                                                'text-sm font-medium text-gray-950 dark:text-white',
                                                'truncate' => $isItemLabelTruncated(),
                                                'cursor-pointer select-none' => $isCollapsible,
                                            ])
                                        >
                                            {{ $itemLabel }}
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
                                class="fi-fo-repeater-item-content border-t border-gray-100 p-4 dark:border-white/10"
                            >
                                {{ $item }}
                            </div>
                        </li>

                        @if (! $loop->last)
                            @if ($isAddable && $addBetweenAction->isVisible())
                                <li class="flex w-full justify-center">
                                    <div
                                        class="rounded-lg bg-white dark:bg-gray-900"
                                    >
                                        {{ $addBetweenAction(['afterItem' => $uuid]) }}
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
                </x-filament::grid>
            </ul>
        @endif

        @if ($isAddable)
            <div class="flex justify-center">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
