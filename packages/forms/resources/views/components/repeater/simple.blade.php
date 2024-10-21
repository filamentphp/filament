@php
    use Filament\Forms\Components\Actions\Action;

    $containers = $getChildComponentContainers();

    $addAction = $getAction($getAddActionName());
    $cloneAction = $getAction($getCloneActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $key = $getKey();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-simple-repeater grid gap-y-4'])
        }}
    >
        @if (count($containers))
            <ul>
                <div
                    x-sortable
                    {{
                        (new ComponentAttributeBag)
                            ->grid($getGridColumns())
                            ->merge([
                                'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                                'wire:end.stop' => 'mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                            ], escape: false)
                            ->class(['gap-4'])
                    }}
                >
                    @foreach ($containers as $uuid => $item)
                        @php
                            $visibleExtraItemActions = array_filter(
                                $extraItemActions,
                                fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                            );
                            $cloneAction = $cloneAction(['item' => $uuid]);
                            $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                            $deleteAction = $deleteAction(['item' => $uuid]);
                            $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                            $moveDownAction = $moveDownAction(['item' => $uuid])->disabled($loop->last);
                            $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                            $moveUpAction = $moveUpAction(['item' => $uuid])->disabled($loop->first);
                            $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                            $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                        @endphp

                        <li
                            wire:key="{{ $item->getLivewireKey() }}.item"
                            x-sortable-item="{{ $uuid }}"
                            class="fi-fo-repeater-item simple flex justify-start gap-x-3"
                        >
                            <div class="flex-1">
                                {{ $item }}
                            </div>

                            @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $visibleExtraItemActions)
                                <ul class="flex items-center gap-x-1">
                                    @if ($reorderActionIsVisible)
                                        <li x-sortable-handle>
                                            {{ $reorderAction }}
                                        </li>
                                    @endif

                                    @if ($moveUpActionIsVisible || $moveDownActionIsVisible)
                                        <li
                                            class="flex items-center justify-center"
                                        >
                                            {{ $moveUpAction }}
                                        </li>

                                        <li
                                            class="flex items-center justify-center"
                                        >
                                            {{ $moveDownAction }}
                                        </li>
                                    @endif

                                    @foreach ($visibleExtraItemActions as $extraItemAction)
                                        <li>
                                            {{ $extraItemAction(['item' => $uuid]) }}
                                        </li>
                                    @endforeach

                                    @if ($cloneActionIsVisible)
                                        <li>
                                            {{ $cloneAction }}
                                        </li>
                                    @endif

                                    @if ($deleteActionIsVisible)
                                        <li>
                                            {{ $deleteAction }}
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </div>
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <div class="flex justify-center">
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
