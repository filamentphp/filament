<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $containers = $getChildComponentContainers();

        $addAction = $getAction($getAddActionName());
        $cloneAction = $getAction($getCloneActionName());
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
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="flex gap-x-3">
                <span
                    x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                >
                    {{ $getAction('collapseAll') }}
                </span>

                <span
                    x-on:click="$dispatch('repeater-expand', '{{ $statePath }}')"
                >
                    {{ $getAction('expandAll') }}
                </span>
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
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-sortable-item="{{ $uuid }}"
                            class="fi-fo-repeater-item rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
                        >
                            @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || filled($itemLabel) || $isCloneable || $isDeletable || $isCollapsible)
                                <div
                                    class="flex items-center gap-x-3 px-4 py-2"
                                >
                                    @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons)
                                        <ul class="-ms-1.5 flex">
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
                                            class="truncate text-sm font-medium text-gray-950 dark:text-white"
                                        >
                                            {{ $itemLabel }}
                                        </h4>
                                    @endif

                                    @if ($isCloneable || $isDeletable || $isCollapsible)
                                        <ul class="-me-1.5 ms-auto flex">
                                            @if ($cloneAction->isVisible())
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
                                class="border-t border-gray-100 p-4 dark:border-white/10"
                                x-show="! isCollapsed"
                            >
                                {{ $item }}
                            </div>
                        </li>
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
