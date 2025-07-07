@php
    use Filament\Actions\Action;
    use Filament\Support\Enums\Alignment;

    $fieldWrapperView = $getFieldWrapperView();
    $items = $getItems();
    $blockPickerBlocks = $getBlockPickerBlocks();
    $blockPickerColumns = $getBlockPickerColumns();
    $blockPickerWidth = $getBlockPickerWidth();
    $hasBlockPreviews = $hasBlockPreviews();
    $hasInteractiveBlockPreviews = $hasInteractiveBlockPreviews();

    $addAction = $getAction($getAddActionName());
    $addActionAlignment = $getAddActionAlignment();
    $addBetweenAction = $getAction($getAddBetweenActionName());
    $cloneAction = $getAction($getCloneActionName());
    $collapseAllAction = $getAction($getCollapseAllActionName());
    $editAction = $getAction($getEditActionName());
    $expandAllAction = $getAction($getExpandAllActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
    $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();

    $key = $getKey();
    $statePath = $getStatePath();

    $blockLabelHeadingTag = $getHeadingTag();
    $isBlockLabelTruncated = $isBlockLabelTruncated();
    $labelBetweenItems = $getLabelBetweenItems();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-builder',
                    'fi-collapsible' => $isCollapsible,
                ])
        }}
    >
        @if ($collapseAllActionIsVisible || $expandAllActionIsVisible)
            <div
                @class([
                    'fi-fo-builder-actions',
                    'fi-hidden' => count($items) < 2,
                ])
            >
                @if ($collapseAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('builder-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('builder-expand', '{{ $statePath }}')"
                    >
                        {{ $expandAllAction }}
                    </span>
                @endif
            </div>
        @endif

        @if (count($items))
            <ul
                x-sortable
                data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                wire:end.stop="mountAction('reorder', { items: $event.target.sortable.toArray() }, { schemaComponent: '{{ $key }}' })"
                class="fi-fo-builder-items"
            >
                @php
                    $hasBlockLabels = $hasBlockLabels();
                    $hasBlockIcons = $hasBlockIcons();
                    $hasBlockNumbers = $hasBlockNumbers();
                @endphp

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
                        $editAction = $editAction(['item' => $itemKey]);
                        $editActionIsVisible = $hasBlockPreviews && $editAction->isVisible();
                        $moveDownAction = $moveDownAction(['item' => $itemKey])->disabled($loop->last);
                        $moveDownActionIsVisible = $isReorderableWithButtons && $moveDownAction->isVisible();
                        $moveUpAction = $moveUpAction(['item' => $itemKey])->disabled($loop->first);
                        $moveUpActionIsVisible = $isReorderableWithButtons && $moveUpAction->isVisible();
                        $reorderActionIsVisible = $isReorderableWithDragAndDrop && $reorderAction->isVisible();
                    @endphp

                    <li
                        wire:ignore.self
                        wire:key="{{ $item->getLivewireKey() }}.item"
                        x-data="{
                            isCollapsed: @js($isCollapsed($item)),
                        }"
                        x-on:builder-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:builder-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                        x-on:expand="isCollapsed = false"
                        x-sortable-item="{{ $itemKey }}"
                        {{
                            $item->getParentComponent()->getExtraAttributeBag()
                                ->class(['fi-fo-builder-item'])
                        }}
                        x-bind:class="{ 'fi-collapsed': isCollapsed }"
                    >
                        @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || $hasBlockIcons || $hasBlockLabels || $editActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                            <div
                                @if ($isCollapsible)
                                    x-on:click.stop="isCollapsed = !isCollapsed"
                                @endif
                                class="fi-fo-builder-item-header"
                            >
                                @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible)
                                    <ul
                                        class="fi-fo-builder-item-header-start-actions"
                                    >
                                        @if ($reorderActionIsVisible)
                                            <li
                                                x-sortable-handle
                                                x-on:click.stop
                                            >
                                                {{ $reorderAction }}
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
                                    </ul>
                                @endif

                                @php
                                    $blockIcon = $item->getParentComponent()->getIcon($item->getRawState(), $itemKey);
                                @endphp

                                @if ($hasBlockIcons && filled($blockIcon))
                                    {{ \Filament\Support\generate_icon_html($blockIcon, (new \Illuminate\View\ComponentAttributeBag)->class(['fi-fo-builder-item-header-icon'])) }}
                                @endif

                                @if ($hasBlockLabels)
                                    <{{ $blockLabelHeadingTag }}
                                        @class([
                                            'fi-fo-builder-item-header-label',
                                            'fi-truncated' => $isBlockLabelTruncated,
                                        ])
                                    >
                                        {{ $item->getParentComponent()->getLabel($item->getRawState(), $itemKey) }}

                                        @if ($hasBlockNumbers)
                                            {{ $loop->iteration }}
                                        @endif
                                    </{{ $blockLabelHeadingTag }}>
                                @endif

                                @if ($editActionIsVisible || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                                    <ul
                                        class="fi-fo-builder-item-header-end-actions"
                                    >
                                        @foreach ($visibleExtraItemActions as $extraItemAction)
                                            <li x-on:click.stop>
                                                {{ $extraItemAction(['item' => $itemKey]) }}
                                            </li>
                                        @endforeach

                                        @if ($editActionIsVisible)
                                            <li x-on:click.stop>
                                                {{ $editAction }}
                                            </li>
                                        @endif

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

                                        @if ($isCollapsible)
                                            <li
                                                class="fi-fo-builder-item-header-collapsible-actions"
                                                x-on:click.stop="isCollapsed = !isCollapsed"
                                            >
                                                <div
                                                    class="fi-fo-builder-item-header-collapse-action"
                                                >
                                                    {{ $getAction('collapse') }}
                                                </div>

                                                <div
                                                    class="fi-fo-builder-item-header-expand-action"
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
                            @class([
                                'fi-fo-builder-item-content',
                                'fi-fo-builder-item-content-has-preview' => $hasBlockPreviews && $item->getParentComponent()->hasPreview(),
                            ])
                        >
                            @if ($hasBlockPreviews && $item->getParentComponent()->hasPreview())
                                <div
                                    @class([
                                        'fi-fo-builder-item-preview',
                                        'fi-interactive' => $hasInteractiveBlockPreviews,
                                    ])
                                >
                                    {{ $item->getParentComponent()->renderPreview($item->getRawState()) }}
                                </div>

                                @if ($editActionIsVisible && (! $hasInteractiveBlockPreviews))
                                    <div
                                        class="fi-fo-builder-item-preview-edit-overlay"
                                        role="button"
                                        x-on:click.stop="{{ '$wire.mountAction(\'edit\', { item: \'' . $itemKey . '\' }, { schemaComponent: \'' . $key . '\' })' }}"
                                    ></div>
                                @endif
                            @else
                                {{ $item }}
                            @endif
                        </div>
                    </li>

                    @if (! $loop->last)
                        @if ($isAddable && $addBetweenAction(['afterItem' => $itemKey])->isVisible())
                            <li class="fi-fo-builder-add-between-items-ctn">
                                <div class="fi-fo-builder-add-between-items">
                                    <div class="fi-fo-builder-block-picker-ctn">
                                        <x-filament-forms::builder.block-picker
                                            :action="$addBetweenAction"
                                            :after-item="$itemKey"
                                            :columns="$blockPickerColumns"
                                            :blocks="$blockPickerBlocks"
                                            :key="$key"
                                            :width="$blockPickerWidth"
                                        >
                                            <x-slot name="trigger">
                                                {{ $addBetweenAction(['afterItem' => $itemKey]) }}
                                            </x-slot>
                                        </x-filament-forms::builder.block-picker>
                                    </div>
                                </div>
                            </li>
                        @elseif (filled($labelBetweenItems))
                            <li class="fi-fo-builder-label-between-items-ctn">
                                <span class="fi-fo-builder-label-between-items">
                                    {{ $labelBetweenItems }}
                                </span>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable && $addAction->isVisible())
            <x-filament-forms::builder.block-picker
                :action="$addAction"
                :blocks="$blockPickerBlocks"
                :columns="$blockPickerColumns"
                :key="$key"
                :width="$blockPickerWidth"
                :class="($addActionAlignment instanceof Alignment) ? ('fi-align-' . $addActionAlignment->value) : $addActionAlignment"
            >
                <x-slot name="trigger">
                    {{ $addAction }}
                </x-slot>
            </x-filament-forms::builder.block-picker>
        @endif
    </div>
</x-dynamic-component>
