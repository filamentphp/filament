@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    $fieldWrapperView = $getFieldWrapperView();

    $items = $getItems();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
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

    $tableColumns = $getTableColumns();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{ $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-table-repeater']) }}
    >
        @if (count($items))
            <table class="fi-absolute-positioning-context">
                <thead>
                    <tr>
                        @if ((count($items) > 1) && ($isReorderableWithButtons || $isReorderableWithDragAndDrop))
                            <th
                                class="fi-fo-table-repeater-empty-header-cell"
                            ></th>
                        @endif

                        @foreach ($tableColumns as $column)
                            <th
                                @class([
                                    'fi-wrapped' => $column->canHeaderWrap(),
                                    (($columnAlignment = $column->getAlignment()) instanceof Alignment) ? ('fi-align-' . $columnAlignment->value) : $columnAlignment,
                                ])
                                @style([
                                    ('width: ' . ($columnWidth = $column->getWidth())) => filled($columnWidth),
                                ])
                            >
                                @if (! $column->isHeaderLabelHidden())
                                    {{ $column->getLabel() }}@if ($column->isMarkedAsRequired())<sup class="fi-fo-table-repeater-header-required-mark">*</sup>
                                    @endif
                                @else
                                    <span class="fi-sr-only">
                                        {{ $column->getLabel() }}
                                    </span>
                                @endif
                            </th>
                        @endforeach

                        @if (count($extraItemActions) || $isCloneable || $isDeletable)
                            <th
                                class="fi-fo-table-repeater-empty-header-cell"
                            ></th>
                        @endif
                    </tr>
                </thead>

                @if (count($items))
                    <tbody
                        x-sortable
                        {{ (new ComponentAttributeBag)
                                ->merge([
                                    'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                                    'wire:end.stop' => 'mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
                                ], escape: false) }}
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

                            <tr
                                wire:key="{{ $item->getLivewireKey() }}.item"
                                x-sortable-item="{{ $itemKey }}"
                            >
                                @if ((count($items) > 1) && ($isReorderableWithButtons || $isReorderableWithDragAndDrop))
                                    <td>
                                        @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible)
                                            <div
                                                class="fi-fo-table-repeater-actions"
                                            >
                                                @if ($reorderActionIsVisible)
                                                    <div
                                                        x-sortable-handle
                                                        x-on:click.stop
                                                    >
                                                        {{ $reorderAction }}
                                                    </div>
                                                @endif

                                                @if ($moveUpActionIsVisible || $moveDownActionIsVisible)
                                                    <div x-on:click.stop>
                                                        {{ $moveUpAction }}
                                                    </div>

                                                    <div x-on:click.stop>
                                                        {{ $moveDownAction }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endif

                                @php
                                    $counter = 0
                                @endphp

                                @foreach ($item->getComponents(withHidden: true) as $component)
                                    @php
                                        throw_unless(
                                            $component instanceof \Filament\Forms\Components\Field || $component instanceof \Filament\Infolists\Components\Entry,
                                            new \Exception('Table repeaters must only contain form fields and infolist entries, but [' . $component::class . '] was used.'),
                                        );
                                    @endphp

                                    @if (count($tableColumns) > $counter)
                                        @if ($component instanceof \Filament\Forms\Components\Hidden)
                                            {{ $component }}
                                        @else
                                            @php
                                                $counter++
                                            @endphp

                                            @if ($component->isVisible())
                                                <td>
                                                    {{ $component }}
                                                </td>
                                            @else
                                                <td class="fi-hidden"></td>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach

                                @if (count($extraItemActions) || $isCloneable || $isDeletable)
                                    <td>
                                        @if ($visibleExtraItemActions || $cloneActionIsVisible || $deleteActionIsVisible)
                                            <div
                                                class="fi-fo-table-repeater-actions"
                                            >
                                                @foreach ($visibleExtraItemActions as $extraItemAction)
                                                    <div x-on:click.stop>
                                                        {{ $extraItemAction(['item' => $itemKey]) }}
                                                    </div>
                                                @endforeach

                                                @if ($cloneActionIsVisible)
                                                    <div x-on:click.stop>
                                                        {{ $cloneAction }}
                                                    </div>
                                                @endif

                                                @if ($deleteActionIsVisible)
                                                    <div x-on:click.stop>
                                                        {{ $deleteAction }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <div
                @class([
                    'fi-fo-table-repeater-add',
                    ($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment,
                ])
            >
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
