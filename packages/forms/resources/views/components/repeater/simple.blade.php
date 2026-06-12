@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
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

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-simple-repeater'])
        }}
    >
        @if (count($items))
            <ul
                x-sortable
                {{
                    (new ComponentAttributeBag)
                        ->grid($getGridColumns())
                        ->merge([
                            'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                            'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                        ], escape: false)
                        ->class(['fi-fo-simple-repeater-items'])
                }}
            >
                @foreach ($items as $itemKey => $item)
                    @php
                        $visibleExtraItemActions = array_filter(
                            $extraItemActions,
                            fn (Action $action): bool => $action(['item' => $itemKey])->isVisible(),
                        );
                        $cloneAction = $cloneAction(['item' => $itemKey]);
                        $cloneActionIsVisible = $isCloneable && $cloneAction->isVisible();
                        $deleteAction = $deleteAction(['item' => $itemKey]);
                        $deleteActionIsVisible = $isDeletable && $deleteAction->isVisible();
                        $moveDownAction = $moveDownAction(['item' => $itemKey])->disabled($loop->last);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                        $moveUpAction = $moveUpAction(['item' => $itemKey])->disabled($loop->first);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                    @endphp

                    <li
                        wire:key="{{ $item->getLivewireKey() }}.item"
                        x-sortable-item="{{ $itemKey }}"
                        class="fi-fo-simple-repeater-item"
                    >
                        <div class="fi-fo-simple-repeater-item-content">
                            {{ $item }}
                        </div>

                        @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $visibleExtraItemActions)
                            <ul class="fi-fo-simple-repeater-item-actions">
                                @if ($reorderActionIsVisible)
                                    <li x-on:click.stop>
                                        {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
                                    </li>
                                @endif

                                @if ($moveUpActionIsVisible || $moveDownActionIsVisible)
                                    <li x-on:click.stop>
                                        {{ $moveUpAction }}
                                    </li>

                                    <li x-on:click.stop>
                                        {{ $moveDownAction }}
                                    </li>
                                @endif

                                @foreach ($visibleExtraItemActions as $extraItemAction)
                                    <li x-on:click.stop>
                                        {{ $extraItemAction(['item' => $itemKey]) }}
                                    </li>
                                @endforeach

                                @if ($cloneActionIsVisible)
                                    <li x-on:click.stop>
                                        {{ $cloneAction }}
                                    </li>
                                @endif

                                @if ($deleteActionIsVisible)
                                    <li x-on:click.stop>
                                        {{ $deleteAction }}
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <div
                @class([
                    'fi-fo-simple-repeater-add',
                    ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                ])
            >
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
