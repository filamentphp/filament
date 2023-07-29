<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $containers = $getChildComponentContainers();

        $addAction = $getAction($getAddActionName());
        $addBetweenAction = $getAction($getAddBetweenActionName());
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
                ->class(['fi-fo-builder grid gap-y-4'])
        }}
    >
        @if ((count($containers) > 1) && $isCollapsible)
            <div class="flex gap-x-3">
                <span
                    x-on:click="$dispatch('builder-collapse', '{{ $statePath }}')"
                >
                    {{ $getAction('collapseAll') }}
                </span>

                <span
                    x-on:click="$dispatch('builder-expand', '{{ $statePath }}')"
                >
                    {{ $getAction('expandAll') }}
                </span>
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
                        x-sortable-item="{{ $uuid }}"
                        class="fi-fo-builder-item rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
                    >
                        @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || $hasBlockLabels || $isCloneable || $isDeletable || $isCollapsible)
                            <div class="flex items-center gap-x-3 px-4 py-2">
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

                                @if ($hasBlockLabels)
                                    <h4
                                        class="truncate text-sm font-medium text-gray-950 dark:text-white"
                                    >
                                        @php
                                            $block = $item->getParentComponent();

                                            $block->labelState($item->getRawState());
                                        @endphp

                                        {{ $item->getParentComponent()->getLabel() }}

                                        @php
                                            $block->labelState(null);
                                        @endphp

                                        @if ($hasBlockNumbers)
                                            {{ $loop->iteration }}
                                        @endif
                                    </h4>
                                @endif

                                @if ($isCloneable || $isDeletable || $isCollapsible)
                                    <ul class="-me-1.5 ms-auto flex">
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
                            class="border-t border-gray-100 p-4 dark:border-white/10"
                            x-show="! isCollapsed"
                        >
                            {{ $item }}
                        </div>
                    </li>

                    @if ((! $loop->last) && $isAddable)
                        <li class="relative -top-2 !mt-0 h-0">
                            <div
                                class="flex w-full justify-center opacity-0 transition duration-75 hover:opacity-100"
                            >
                                <div
                                    class="rounded-lg bg-white dark:bg-gray-900"
                                >
                                    <x-filament-forms::builder.block-picker
                                        :action="$addBetweenAction"
                                        :after-item="$uuid"
                                        :blocks="$getBlocks()"
                                        :state-path="$statePath"
                                    >
                                        <x-slot name="trigger">
                                            {{ $addBetweenAction }}
                                        </x-slot>
                                    </x-filament-forms::builder.block-picker>
                                </div>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable)
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
