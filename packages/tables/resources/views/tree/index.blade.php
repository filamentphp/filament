@php
    use Filament\Support\Enums\Alignment;

    $treeChildrenRelationship ??= 'children';
    $isTreeCollapsible ??= false;
    $isTreeCollapsedByDefault ??= false;
    $reorderAnimationDuration ??= 300;
@endphp

<div
    x-data="filamentTableTree({
                collapsible: @js($isTreeCollapsible),
                collapseByDefault: @js($isTreeCollapsedByDefault),
                reorderable: @js($isReorderable),
                reorderMethod: 'reorderTreeTable',
                $wire,
            })"
    class="fi-ta-tree"
>
    <div
        @if ($isReorderable)
            x-sortable
            x-on:end.stop="handleTreeReorder($event)"
            data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
            x-sortable-group="fi-ta-tree"
        @endif
        x-ref="treeRoot"
        data-tree-root
        class="fi-ta-tree-root space-y-2"
    >
        @foreach ($records as $record)
            @include('filament-tables::tree.node', [
                'record' => $record,
                'treeChildrenRelationship' => $treeChildrenRelationship,
                'treeTitleAttribute' => $treeTitleAttribute,
                'isTreeCollapsible' => $isTreeCollapsible,
                'isTreeCollapsedByDefault' => $isTreeCollapsedByDefault,
                'isReorderable' => $isReorderable,
                'defaultRecordActions' => $defaultRecordActions,
                'reorderAnimationDuration' => $reorderAnimationDuration,
            ])
        @endforeach
    </div>
</div>
