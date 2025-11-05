@php
    use Filament\Actions\Action;
    use Filament\Actions\BulkAction;
    use Filament\Support\ArrayRecord;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\View\TablesIconAlias;
    use Illuminate\Database\Eloquent\Model;

    use function Filament\Support\generate_href_html;
    use function Filament\Support\generate_icon_html;

    $recordKey = $record instanceof Model
        ? (string) $record->getKey()
        : (string) ($record[ArrayRecord::getKeyName()] ?? null);
    $recordParent = $record instanceof Model
        ? $record->getAttribute('__filament_tree_parent')
        : ($record['__filament_tree_parent'] ?? null);
    $recordDepth = $record instanceof Model
        ? $record->getAttribute('__filament_tree_depth')
        : ($record['__filament_tree_depth'] ?? 0);

    $children = $record instanceof Model
        ? ($record->getRelation($treeChildrenRelationship) ?? collect())
        : collect($record[$treeChildrenRelationship] ?? []);

    $childrenCount = $children->count();

    $recordAction = $getRecordAction($record);
    $recordUrl = $getRecordUrl($record);
    $openRecordUrlInNewTab = $recordUrl ? $shouldOpenRecordUrlInNewTab($record) : false;

    $recordTitleAttribute = $treeTitleAttribute ?? $getRecordTitleAttribute();

    $recordLabel = $record instanceof Model
        ? $getRecordTitle($record)
        : (
            ($recordTitleAttribute && array_key_exists($recordTitleAttribute, $record))
                ? $record[$recordTitleAttribute]
                : ($record['title'] ?? $record['name'] ?? ($record[ArrayRecord::getKeyName()] ?? (string) $recordKey))
        );

    $recordActions = array_reduce(
        $defaultRecordActions,
        function (array $carry, $action) use ($record): array {
            $action = $action->getClone();

            if (! $action instanceof BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                return $carry;
            }

            $carry[] = $action;

            return $carry;
        },
        initial: [],
    );

    $recordWireClickAction = null;

    if ($recordAction) {
        $recordWireClickAction = (class_exists($recordAction) && is_subclass_of($recordAction, Action::class))
            ? "mountTableAction('{$recordAction}', '{$recordKey}')"
            : "{$recordAction}('{$recordKey}')";
    }
@endphp

<div
    wire:key="{{ $this->getId() }}.tree.records.{{ $recordKey }}"
    x-data="{ nodeId: @js($recordKey) }"
    x-sortable-item="{{ $recordKey }}"
    data-tree-node
    class="fi-ta-tree-node rounded-xl border border-transparent bg-white shadow-sm ring-1 ring-gray-950/5 transition duration-75 dark:bg-white/5 dark:ring-white/10"
>
    <div
        class="fi-ta-tree-node-row flex items-center gap-x-3 px-3 py-2"
        style="
            padding-inline-start: calc(
                {{ max((int) $recordDepth, 0) }} * 1.5rem + 0.75rem
            );
        "
    >
        @if ($isReorderable)
            <button class="fi-ta-reorder-handle fi-icon-btn" type="button">
                {{ generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE) }}
            </button>
        @endif

        @if ($isTreeCollapsible && $childrenCount)
            <button
                type="button"
                class="fi-ta-tree-collapse-btn fi-icon-btn"
                x-on:click="toggleNode(nodeId)"
                x-bind:aria-expanded="! isCollapsed(nodeId)"
            >
                {{ generate_icon_html(Heroicon::ChevronDown, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON) }}
            </button>
        @elseif ($childrenCount)
            <div class="h-6 w-6"></div>
        @endif

        <div class="fi-ta-tree-node-label flex flex-1 items-center gap-x-2">
            @if ($recordUrl)
                <a
                    {{ generate_href_html($recordUrl, $openRecordUrlInNewTab, hasNestedClickEventHandler: true) }}
                    class="fi-ta-tree-node-link hover:text-primary-600 focus:ring-primary-600 dark:hover:text-primary-400 text-sm font-medium text-gray-950 transition focus:ring-2 focus:outline-none dark:text-white"
                >
                    {{ $recordLabel }}
                </a>
            @elseif ($recordWireClickAction)
                <button
                    type="button"
                    wire:click="{{ $recordWireClickAction }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $recordWireClickAction }}"
                    class="hover:text-primary-600 focus:ring-primary-600 dark:hover:text-primary-400 text-left text-sm font-medium text-gray-950 transition focus:ring-2 focus:outline-none disabled:opacity-70 dark:text-white"
                >
                    {{ $recordLabel }}
                </button>
            @else
                <span class="text-sm font-medium text-gray-950 dark:text-white">
                    {{ $recordLabel }}
                </span>
            @endif
        </div>

        @if (count($recordActions))
            <div
                class="fi-ta-tree-node-actions ml-auto flex items-center gap-x-2"
            >
                @foreach ($recordActions as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endif
    </div>

    @if ($childrenCount)
        <div
            data-tree-children
            class="fi-ta-tree-children space-y-2 border-t border-gray-200/80 px-3 py-2 dark:border-white/10"
            x-show="! isCollapsed(nodeId)"
            x-collapse
            @if ($isReorderable)
                x-sortable
                x-on:end="handleTreeReorder($event)"
                x-sortable-group="fi-ta-tree"
                data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
            @endif
        >
            @foreach ($children as $childRecord)
                @include('filament-tables::tree.node', [
                    'record' => $childRecord,
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
    @endif
</div>
