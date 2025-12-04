@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Illuminate\Support\Js;
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

    $isCompact = $isCompact();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{ $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-table-repeater',
                    'fi-compact' => $isCompact,
                ]) }}
    >
        @if (count($items))
            <table>
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

                <tbody
                    x-sortable
                    {{ (new ComponentAttributeBag)
                            ->merge([
                                'data-sortable-animation-duration' => $getReorderAnimationDuration(),
                                'x-on:end.stop' => '$wire.mountAction(\'reorder\', { items: $event.target.sortable.toArray() }, { schemaComponent: \'' . $key . '\' })',
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
                            $itemStatePath = $item->getStatePath();
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
                                                <div x-on:click.stop>
                                                    {{ $reorderAction->extraAttributes(['x-sortable-handle' => true], merge: true) }}
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

                            @foreach ($item->getComponents(withHidden: true) as $schemaComponent)
                                @php
                                    throw_unless(
                                        $schemaComponent instanceof \Filament\Schemas\Components\Component,
                                        new \Exception('Table repeaters must only contain schema components, but [' . $schemaComponent::class . '] was used.'),
                                    );
                                @endphp

                                @if (count($tableColumns) > $counter)
                                    @if ($schemaComponent instanceof \Filament\Forms\Components\Hidden)
                                        {{ $schemaComponent }}
                                    @else
                                        @php
                                            $counter++
                                        @endphp

                                        @if ($schemaComponent->isVisible())
                                            @php
                                                $schemaComponentStatePath = $schemaComponent->getStatePath();
                                                $currentColumn = $tableColumns[$counter - 1] ?? null;
                                                $columnVerticalAlignment = $currentColumn?->getVerticalAlignment();
                                            @endphp

                                            <td
                                                @class([
                                                    ($columnVerticalAlignment instanceof VerticalAlignment) ? ('fi-vertical-align-' . $columnVerticalAlignment->value) : (is_string($columnVerticalAlignment) ? $columnVerticalAlignment : ''),
                                                ])
                                                x-data="filamentSchemaComponent({
                                                    path: @js($schemaComponentStatePath),
                                                    containerPath: @js($itemStatePath),
                                                    $wire,
                                                })"
                                                @if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs())
                                                    x-init="{{ implode(';', array_map(
                                                        fn (string $js): string => '$wire.watch(' . Js::from($schemaComponentStatePath) . ', ($state, $old) => isStateChanged($state, $old) && eval(' . Js::from($js) . '))',
                                                        $afterStateUpdatedJs,
                                                    )) }}"
                                                @endif
                                            >
                                                {{ $schemaComponent }}
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
