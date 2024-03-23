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
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-simple-repeater grid gap-y-4'])
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
                    :wire:end.stop="'mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })'"
                    x-sortable
                    :data-sortable-animation-duration="$getReorderAnimationDuration()"
                    class="gap-4"
                >
                    @foreach ($containers as $uuid => $item)
                        <li
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            x-sortable-item="{{ $uuid }}"
                            class="fi-fo-repeater-item simple flex justify-start gap-x-3"
                        >
                            <div class="flex-1">
                                {{ $item }}
                            </div>

                            @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || $isCloneable || $isDeletable)
                                <ul class="flex items-center gap-x-1">
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
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </x-filament::grid>
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <div class="flex justify-center">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
