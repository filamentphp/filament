@php
    use Filament\Forms\Components\Actions\Action;
    use Filament\Support\Enums\Alignment;

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
    $extraItemActions = $getExtraItemActions();

    $isAddable = $isAddable();
    $isCloneable = $isCloneable();
    $isCollapsible = $isCollapsible();
    $isDeletable = $isDeletable();
    $isReorderableWithButtons = $isReorderableWithButtons();
    $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

    $collapseAllActionIsVisible = $isCollapsible && $collapseAllAction->isVisible();
    $expandAllActionIsVisible = $isCollapsible && $expandAllAction->isVisible();

    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-repeater grid gap-y-4'])
        }}
    >
        @if ($collapseAllActionIsVisible || $expandAllActionIsVisible)
            <div
                @class([
                    'flex gap-x-3',
                    'hidden' => count($containers) < 2,
                ])
            >
                @if ($collapseAllActionIsVisible)
                    <span
                        x-on:click="$dispatch('repeater-collapse', '{{ $statePath }}')"
                    >
                        {{ $collapseAllAction }}
                    </span>
                @endif

                @if ($expandAllActionIsVisible)
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
                    :data-sortable-animation-duration="$getReorderAnimationDuration()"
                    class="items-start gap-4"
                >
                    @foreach ($containers as $uuid => $item)
                        @php
                            $itemLabel = $getItemLabel($uuid);
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
                            wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                            x-data="{
                                isCollapsed: @js($isCollapsed($item)),
                            }"
                            x-on:expand="isCollapsed = false"
                            x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                            x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                            x-sortable-item="{{ $uuid }}"
                            class="fi-fo-repeater-item divide-y divide-gray-100 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-white/5 dark:ring-white/10"
                            x-bind:class="{ 'fi-collapsed overflow-hidden': isCollapsed }"
                        >
                            @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible || filled($itemLabel) || $cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                                <div
                                    @if ($isCollapsible)
                                        x-on:click.stop="isCollapsed = !isCollapsed"
                                    @endif
                                    @class([
                                        'fi-fo-repeater-item-header flex items-center gap-x-3 overflow-hidden px-4 py-3',
                                        'cursor-pointer select-none' => $isCollapsible,
                                    ])
                                >
                                    @if ($reorderActionIsVisible || $moveUpActionIsVisible || $moveDownActionIsVisible)
                                        <ul class="flex items-center gap-x-3">
                                            @if ($reorderActionIsVisible)
                                                <li
                                                    x-sortable-handle
                                                    x-on:click.stop
                                                >
                                                    {{ $reorderAction }}
                                                </li>
                                            @endif

                                            @if ($moveUpActionIsVisible || $moveDownActionIsVisible)
                                                <li
                                                    x-on:click.stop
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveUpAction }}
                                                </li>

                                                <li
                                                    x-on:click.stop
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveDownAction }}
                                                </li>
                                            @endif
                                        </ul>
                                    @endif

                                    @if (filled($itemLabel))
                                        <h4
                                            @class([
                                                'text-sm font-medium text-gray-950 dark:text-white',
                                                'truncate' => $isItemLabelTruncated(),
                                            ])
                                        >
                                            {{ $itemLabel }}
                                        </h4>
                                    @endif

                                    @if ($cloneActionIsVisible || $deleteActionIsVisible || $isCollapsible || $visibleExtraItemActions)
                                        <ul
                                            class="ms-auto flex items-center gap-x-3"
                                        >
                                            @foreach ($visibleExtraItemActions as $extraItemAction)
                                                <li x-on:click.stop>
                                                    {{ $extraItemAction(['item' => $uuid]) }}
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
                                class="fi-fo-repeater-item-content p-4"
                            >
                                {{ $item }}
                            </div>
                        </li>

                        @if (! $loop->last)
                            @if ($isAddable && $addBetweenAction(['afterItem' => $uuid])->isVisible())
                                <li class="flex w-full justify-center">
                                    <div
                                        class="fi-fo-repeater-add-between-action-ctn rounded-lg bg-white dark:bg-gray-900"
                                    >
                                        {{ $addBetweenAction(['afterItem' => $uuid]) }}
                                    </div>
                                </li>
                            @elseif (filled($labelBetweenItems = $getLabelBetweenItems()))
                                <li
                                    class="relative border-t border-gray-200 dark:border-white/10"
                                >
                                    <span
                                        class="absolute -top-3 left-3 px-1 text-sm font-medium"
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

        @if ($isAddable && $addAction->isVisible())
            <div
                @class([
                    'flex',
                    match ($getAddActionAlignment()) {
                        Alignment::Start, Alignment::Left => 'justify-start',
                        Alignment::Center, null => 'justify-center',
                        Alignment::End, Alignment::Right => 'justify-end',
                        default => $alignment,
                    },
                ])
            >
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
