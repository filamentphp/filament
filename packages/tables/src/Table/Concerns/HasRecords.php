<?php

namespace Filament\Tables\Table\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Table;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\Factory;
use Stringable;
use UnhandledMatchError;

use function Filament\Support\get_model_label;
use function Filament\Support\locale_has_pluralization;

trait HasRecords
{
    protected bool | Closure $allowsDuplicates = false;

    protected string | Closure | null $modelLabel = null;

    protected string | Closure | null $pluralModelLabel = null;

    protected string | Closure | null $recordTitle = null;

    protected bool $isRecordTitleInherited = false;

    protected string | Closure | null $recordTitleAttribute = null;

    protected ?Closure $dataSource = null;

    protected ?string $cachedModel = null;

    protected bool $hasCachedModel = false;

    protected ?bool $cachedHasPivotRecordKeys = null;

    protected ?Closure $resolveSelectedRecordsUsing = null;

    public function records(?Closure $dataSource): static
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function resolveSelectedRecordsUsing(?Closure $callback): static
    {
        $this->resolveSelectedRecordsUsing = $callback;

        return $this;
    }

    public function allowDuplicates(bool | Closure $condition = true): static
    {
        $this->allowsDuplicates = $condition;
        $this->cachedHasPivotRecordKeys = null;

        return $this;
    }

    public function modelLabel(string | Closure | null $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function pluralModelLabel(string | Closure | null $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function recordTitle(string | Closure | null $title): static
    {
        $this->recordTitle = $title;
        $this->isRecordTitleInherited = false;

        return $this;
    }

    public function markRecordTitleAsInherited(): static
    {
        $this->isRecordTitleInherited = true;

        return $this;
    }

    public function recordTitleAttribute(string | Closure | null $attribute): static
    {
        $this->recordTitleAttribute = $attribute;

        if ($this->isRecordTitleInherited) {
            $this->recordTitle(null);
        }

        return $this;
    }

    public function getRecords(): Collection | Paginator | CursorPaginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function hasQuery(): bool
    {
        return ! $this->dataSource;
    }

    public function getDataSource(): ?Closure
    {
        return $this->dataSource;
    }

    public function getResolveSelectedRecordsCallback(): ?Closure
    {
        return $this->resolveSelectedRecordsUsing;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getRecordKey(Model | array $record): string
    {
        return $this->getLivewire()->getTableRecordKey($record);
    }

    /**
     * @return class-string<Model>|null
     */
    public function getModel(): ?string
    {
        if ($this->hasCachedModel) {
            return $this->cachedModel;
        }

        $this->hasCachedModel = true;

        if ($baseQuery = $this->evaluate($this->query)) {
            return $this->cachedModel = $baseQuery->getModel()::class;
        }

        if ($relationshipQuery = $this->getRelationshipQuery()) {
            return $this->cachedModel = $relationshipQuery->getModel()::class;
        }

        return $this->cachedModel = null;
    }

    public function allowsDuplicates(): bool
    {
        return (bool) $this->evaluate($this->allowsDuplicates);
    }

    /** Cached because both underlying calls evaluate Closures and this is hit per row. */
    public function hasPivotRecordKeys(): bool
    {
        return $this->cachedHasPivotRecordKeys ??= (
            ($this->getRelationship() instanceof BelongsToMany)
            && $this->allowsDuplicates()
        );
    }

    public function getModelLabel(): string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        $model = $this->getModel();

        if (filled($model)) {
            return get_model_label($model);
        }

        return __('filament-tables::table.default_model_label');
    }

    public function getPluralModelLabel(): string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        if (locale_has_pluralization()) {
            return Str::plural($this->getModelLabel());
        }

        return $this->getModelLabel();
    }

    public function getRecordTitle(Model $record): string
    {
        $title = $this->evaluate(
            $this->recordTitle,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );

        if (filled($titleAttribute = $this->getRecordTitleAttribute())) {
            $title ??= $record->getAttributeValue($titleAttribute);
        }

        $title ??= $this->getModelLabel();

        if ($title instanceof HasLabel) {
            $title = $title->getLabel() ?? $this->getModelLabel();

            return ($title instanceof Htmlable) && (! $title instanceof Stringable) ? $title->toHtml() : $title;
        }

        if ($title instanceof BackedEnum) {
            return $title->value;
        }

        return $title;
    }

    public function hasCustomRecordTitle(): bool
    {
        return $this->recordTitle !== null;
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->evaluate($this->recordTitleAttribute);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateResultCountHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start();

        if ($records !== null) {
            // The total across all pages, not the current page's count — and since the total is
            // stable across pages, the live region only announces when the result set really
            // changes, not on every pagination click. Non-length-aware paginators fall back to
            // the page count.
            $resultCount = ($records instanceof LengthAwarePaginator)
                ? $records->total()
                : count($records);
            ?>

            <div
                role="status"
                aria-live="polite"
                aria-atomic="true"
                class="fi-sr-only"
            >
                <?= e(trans_choice('filament-tables::table.result_count', $resultCount, ['count' => $resultCount])); ?>
            </div>

            <div
                wire:loading.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>.block
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                role="status"
                aria-live="polite"
                data-testid="table-loading-state"
                class="fi-sr-only"
            >
                <?= e(__('filament-tables::table.loading')); ?>
            </div>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGridRecordsHtml(): string
    {
        $livewire = $this->getLivewire();
        $viewFactory = app('view');
        $records = $this->getRecords();

        ob_start(); ?>
        <div
            <?php if ($this->isReorderable()) { ?>
                x-ref="tableReorderRecords"
                x-on:start.stop="startTableReorder($event)"
                x-on:end.stop="reorderTableRecords($event)"
                x-sortable
                data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()); ?>"
            <?php } ?>
            aria-label="<?= e($this->getPluralModelLabel()); ?>"
            role="list"
            <?php if ($this->hasLoadingSkeleton()) { ?>
                wire:loading.attr.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="inert"
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            <?php } ?>
            <?= e((new FilamentComponentAttributeBag)
                ->when($contentGrid = $this->getContentGrid(), fn (ComponentAttributeBag $attributes) => $attributes->grid($contentGrid))
                ->class([
                    'fi-ta-content',
                    'fi-ta-content-grid' => $contentGrid,
                    'fi-ta-content-grouped' => $livewire->getTableGrouping(),
                ])); ?>
        >
        <?php
        $previousRecord = null;
        $previousRecordGroupKey = null;
        $previousRecordGroupTitle = null;
        ?>

            <?php foreach ($this->iterateRenderLoop($records) as $record) {
                $loop = $viewFactory->getLastLoop();
                $recordKey = $this->getRecordKey($record);
                $recordUrl = $this->getRecordUrl($record);
                $recordAction = $recordUrl ? null : $this->getRecordAction($record);
                $openRecordUrlInNewTab = $this->shouldOpenRecordUrlInNewTab($record);
                $recordGroupKey = $this->getGrouping()?->getStringKey($record);
                $recordGroupTitle = $this->getGrouping()?->getTitle($record, $recordGroupKey);
                $isRecordGroupCollapsible = $this->getGrouping()?->isCollapsible();
                $recordIsSelectable = $this->isRenderSelectionEnabled() && $this->isRecordSelectable($record);

                $this->getCollapsibleColumnsLayout()?->record($record)->recordKey($recordKey);
                $hasCollapsibleColumnsLayout = (bool) $this->getCollapsibleColumnsLayout()?->isVisible();

                $recordActions = $this->getRenderRecordActions($record, $recordKey);
                ?>

                <?php if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle) { ?>
                    <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary()) { ?>
                        <div
                            <?php $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary'; ?>
                            <?php $this->setRenderKey($groupSummaryKey); ?>
                            wire:key="<?= e($groupSummaryKey); ?>"
                            role="listitem"
                            class="fi-ta-group-summary"
                        >
                            <table
                                class="<?= Arr::toCssClasses([
                                    'fi-ta-table',
                                    'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                                    'fi-ta-table-reordering' => $this->isReordering(),
                                ]); ?>"
                            >
                                <tbody>
                                    <?php $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord); ?>

                                    <?= $this->generateSummaryRowHtml(
                                        columns: $this->getVisibleColumns(),
                                        heading: __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                        query: $groupScopedAllTableSummaryQuery,
                                        selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                        extraHeadingColumn: true,
                                        placeholderColumns: false,
                                        attributes: new ComponentAttributeBag([]),
                                    ); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>

                    <?= $this->generateGridGroupHeaderHtml($record, $recordGroupTitle, $isRecordGroupCollapsible, $recordGroupKey); ?>
                <?php } ?>

                <?= $this->generateGridRecordHtml(
                    $hasCollapsibleColumnsLayout,
                    $recordKey,
                    $recordUrl,
                    $recordAction,
                    $recordIsSelectable,
                    $record,
                    $recordGroupTitle,
                    $recordGroupKey,
                    $openRecordUrlInNewTab,
                    $recordActions,
                    $loop,
                ); ?>

                <?php
                $previousRecordGroupKey = $recordGroupKey;
                $previousRecordGroupTitle = $recordGroupTitle;
                $previousRecord = $record;
            } $loop = $viewFactory->getLastLoop(); ?>

            <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary() && $livewire->shouldRenderTrailingGroupedTableSummary($previousRecord)) { ?>
                <div
                    <?php $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary'; ?>
                    <?php $this->setRenderKey($groupSummaryKey); ?>
                    wire:key="<?= e($groupSummaryKey); ?>"
                    role="listitem"
                    class="fi-ta-group-summary"
                >
                    <table
                        class="<?= Arr::toCssClasses([
                            'fi-ta-table',
                            'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                        ]); ?>"
                    >
                        <tbody>
                            <?php $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord); ?>

                            <?= $this->generateSummaryRowHtml(
                                columns: $this->getVisibleColumns(),
                                heading: __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                query: $groupScopedAllTableSummaryQuery,
                                selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                extraHeadingColumn: true,
                                placeholderColumns: false,
                                attributes: new ComponentAttributeBag([]),
                            ); ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateGridRecordHtml(
        bool $hasCollapsibleColumnsLayout,
        string $recordKey,
        ?string $recordUrl,
        ?string $recordAction,
        bool $recordIsSelectable,
        Model | array $record,
        string | Htmlable | null $recordGroupTitle,
        ?string $recordGroupKey,
        bool $openRecordUrlInNewTab,
        array $recordActions,
        \stdClass $loop,
    ): string {
        $viewFactory = app('view');

        ob_start(); ?>
        <div
            <?php if ($hasCollapsibleColumnsLayout) { ?>
                x-data="{ isCollapsed: <?= Js::from($this->getCollapsibleColumnsLayout()->isCollapsed())->toHtml() ?> }"
                x-init="$dispatch('collapsible-table-row-initialized')"
                x-on:collapse-all-table-rows.window="isCollapsed = true"
                x-on:expand-all-table-rows.window="isCollapsed = false"
            <?php } ?>
            role="listitem"
            <?php $this->setRenderKey(e($this->getLivewire()->getId()) . '.table.records.' . e($recordKey)); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.records.<?= e($recordKey); ?>"
            <?php if ($this->isReordering()) { ?>
                x-sortable-item="<?= e($recordKey); ?>"
                x-sortable-handle
            <?php } ?>
            class="<?= Arr::toCssClasses([
                'fi-ta-record',
                'fi-clickable' => $recordUrl || $recordAction,
                'fi-ta-record-with-content-prefix' => $this->isReordering() || $recordIsSelectable,
                'fi-ta-record-with-content-suffix' => $hasCollapsibleColumnsLayout && (! $this->isReordering()),
                ...$this->getRecordClasses($record),
            ]); ?>"
            x-bind:class="{
                <?= e($hasCollapsibleColumnsLayout ? '\'fi-ta-record-collapsed\': isCollapsed,' : ''); ?>
                <?= e($this->getGrouping()?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . Js::from($recordGroupTitle) . '),' : ''); ?>
                'fi-selected': <?= Js::from($recordIsSelectable)->toHtml() ?> && isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>),
            }"
        >
        <?php
        $hasItemBeforeRecordContent = $this->isReordering() || $recordIsSelectable;
        $hasItemAfterRecordContent = $hasCollapsibleColumnsLayout && (! $this->isReordering());
        ?>

            <?= $this->generateMorphMarkerHtml(); ?>
            <?php if ($this->isReordering()) { ?>
                <?= $this->generateRecordReorderControlsHtml($record, $recordKey, $loop); ?>
            <?php } elseif ($recordIsSelectable) { ?>
                <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

            <div class="fi-ta-record-content-ctn">
                <div>
                    <?php if ($recordUrl) { ?>
                        <a
                            <?= e(\Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab, hasNestedClickEventHandler: true)); ?>
                            <?= e($this->getExtraRecordLinkAttributeBag($record)->class(['fi-ta-record-content'])); ?>
                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>
                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </a>
                    <?php } elseif ($recordAction) {
                        $recordWireClickAction = $this->getRecordAction($record)
                            ? "mountAction('{$recordAction}', {}, " . Js::from(['table' => true, 'recordKey' => $recordKey]) . ')'
                            : $recordWireClickAction = "{$recordAction}(" . Js::from($recordKey) . ')';
                        ?>

                        <button
                            type="button"
                            wire:click="<?= e($recordWireClickAction); ?>"
                            wire:loading.attr="disabled"
                            wire:target="<?= e($recordWireClickAction); ?>"
                            class="fi-ta-record-content"
                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>
                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </button>
                    <?php } else { ?>
                        <div
                            class="fi-ta-record-content"
                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>
                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </div>
                    <?php } ?>

                    <?php if ($hasCollapsibleColumnsLayout && (! $this->isReordering())) { ?>
                        <div
                            x-collapse
                            x-show="! isCollapsed"
                            class="fi-ta-record-content fi-collapsible"
                        >
                            <?= e($this->getCollapsibleColumnsLayout()); ?>
                        </div>
                    <?php } ?>
                </div>

                <?php if ($recordActions && (! $this->isReordering())) { ?>
                    <div
                        class="<?= Arr::toCssClasses([
                            'fi-ta-actions fi-wrapped sm:fi-not-wrapped',
                            match ($this->getRenderRecordActionsAlignment() ?? Alignment::Start) {
                                Alignment::Start => 'fi-align-start',
                                Alignment::Center => 'fi-align-center',
                                Alignment::End => 'fi-align-end',
                                default => throw new UnhandledMatchError,
                            } => (bool) ($contentGrid = $this->getContentGrid()),
                            'fi-align-start md:fi-align-end' => ! $contentGrid,
                            'fi-ta-actions-before-columns-position' => $this->getRecordActionsPosition() === RecordActionsPosition::BeforeColumns,
                        ]); ?>"
                    >
                        <?php foreach ($this->iterateRenderLoop($recordActions) as $action) {
                            $loop = $viewFactory->getLastLoop(); ?>
                            <?= e($action); ?>
                        <?php } $loop = $viewFactory->getLastLoop(); ?>
                    </div>
                <?php } ?>
            </div>

            <?php if ($hasCollapsibleColumnsLayout && (! $this->isReordering())) { ?>
                <button
                    aria-label="<?= e(__('filament-tables::table.actions.toggle_record_content.label', ['key' => $recordKey])); ?>"
                    x-bind:aria-expanded="! isCollapsed"
                    type="button"
                    x-on:click="isCollapsed = ! isCollapsed"
                    class="fi-ta-record-collapse-btn fi-icon-btn"
                >
                    <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronDown, alias: TablesIconAlias::COLUMNS_COLLAPSE_BUTTON)); ?>
                </button>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, Column>  $sortableColumns
     */
    protected function generateRecordsTableHtml(array $sortableColumns): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <table
            aria-label="<?= e(filled($tableAccessibleLabel = trim(strip_tags((string) $this->getHeading()))) ? $tableAccessibleLabel : $this->getPluralModelLabel()); ?>"
            class="<?= Arr::toCssClasses([
                'fi-ta-table',
                'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
            ]); ?>"
        >
            <?= $this->generateTableHeaderHtml($sortableColumns); ?>

            <?= $this->generateColumnSearchesHtml(); ?>

            <?= $this->generateRecordsHtml(); ?>

            <?php if (($records !== null) && count($records) && ($contentFooter = $this->getContentFooter())) { ?>
                <tfoot>
                    <tr>
                        <?= $this->generateCustomContentHtml($contentFooter, [
                            'columns' => $this->getVisibleColumns(),
                            'records' => $records,
                        ]); ?>
                    </tr>
                </tfoot>
            <?php } ?>
        </table>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateRecordsHtml(): string
    {
        $livewire = $this->getLivewire();
        $viewFactory = app('view');
        $records = $this->getRecords();
        $isReordering = $this->isReordering();
        $isSelectionEnabled = $this->isRenderSelectionEnabled();
        $recordActionsPosition = $this->getRecordActionsPosition();
        $recordCheckboxPosition = $this->getRecordCheckboxPosition();
        $isStackedOnMobile = $this->isStackedOnMobile();
        $group = $this->getGrouping();
        $isGroupsOnly = $this->isGroupsOnly() && $group;
        $isGroupCollapsible = $group?->isCollapsible() ?? false;
        $morphMarkerStartHtml = $this->generateMorphMarkerHtml();
        $morphMarkerEndHtml = $this->generateMorphMarkerHtml(isEnd: true);

        ob_start(); ?>
        <?php if (count($records)) { ?>
            <tbody
                class="fi-ta-records"
                <?php if ($this->isReorderable()) { ?>
                    x-ref="tableReorderRecords"
                    x-on:start.stop="startTableReorder($event)"
                    x-on:end.stop="reorderTableRecords($event)"
                    x-sortable
                    data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()); ?>"
                <?php } ?>
                <?php if ($this->hasLoadingSkeleton()) { ?>
                    wire:loading.attr.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="inert"
                    wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                <?php } ?>
            >
            <?php
            $isRecordRowStriped = false;
            $previousRecord = null;
            $previousRecordGroupKey = null;
            $previousRecordGroupTitle = null;
            ?>

                <?= $morphMarkerStartHtml; ?>
                <?php foreach ($this->iterateRenderLoop($records, $viewFactory) as $record) {
                    $loop = $viewFactory->getLastLoop();
                    $recordKey = $this->getRecordKey($record);
                    $recordUrl = $this->getRecordUrl($record);
                    $recordAction = $recordUrl ? null : $this->getRecordAction($record);
                    $openRecordUrlInNewTab = $this->shouldOpenRecordUrlInNewTab($record);
                    $recordGroupKey = $group?->getStringKey($record);
                    $recordGroupTitle = $group?->getTitle($record, $recordGroupKey);
                    $recordIsSelectable = $isSelectionEnabled && $this->isRecordSelectable($record);

                    $recordActions = $this->getRenderRecordActions($record, $recordKey);
                    ?>

                    <?php if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle) { ?>
                        <?php if ((! $isReordering) && filled($previousRecordGroupTitle) && $this->hasRenderSummary()) {
                            $groupColumn = $group->getColumn();
                            $groupScopedAllTableSummaryQuery = $group->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                            $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$group->getId(), $previousRecordGroupKey])) . '.summary';
                            $this->setRenderKey($groupSummaryKey);
                            ?>

                            <?= $this->generateSummaryRowHtml(
                                columns: $this->getVisibleColumns(),
                                heading: $isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                query: $groupScopedAllTableSummaryQuery,
                                selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                actions: $this->hasRenderRecordActions(),
                                actionsPosition: $recordActionsPosition,
                                groupColumn: $groupColumn,
                                groupsOnly: $isGroupsOnly,
                                selectionEnabled: $isSelectionEnabled,
                                recordCheckboxPosition: $recordCheckboxPosition,
                                attributes: new ComponentAttributeBag(['wire:key' => $groupSummaryKey]),
                            ); ?>
                        <?php } ?>

                        <?= $this->generateTableGroupHeaderHtml($record, $recordGroupTitle, $recordGroupKey); ?>

                        <?php $isRecordRowStriped = false;
                    } ?>

                    <?= $this->generateRecordHtml(
                        $recordKey,
                        $recordGroupTitle,
                        $recordIsSelectable,
                        $recordAction,
                        $recordUrl,
                        $isRecordRowStriped,
                        $record,
                        $recordActions,
                        $recordGroupKey,
                        $openRecordUrlInNewTab,
                        $loop,
                        $isReordering,
                        $isGroupsOnly,
                        $isGroupCollapsible,
                        $morphMarkerStartHtml,
                        $morphMarkerEndHtml,
                        $isSelectionEnabled,
                        $recordActionsPosition,
                        $recordCheckboxPosition,
                        $isStackedOnMobile,
                        $viewFactory,
                    ); ?>

                    <?php
                    $isRecordRowStriped = ! $isRecordRowStriped;
                    $previousRecord = $record;
                    $previousRecordGroupKey = $recordGroupKey;
                    $previousRecordGroupTitle = $recordGroupTitle;
                } $loop = $viewFactory->getLastLoop(); ?>

                <?php if ((! $isReordering) && filled($previousRecordGroupTitle) && $this->hasRenderSummary() && $livewire->shouldRenderTrailingGroupedTableSummary($previousRecord)) {
                    $groupColumn = $group->getColumn();
                    $groupScopedAllTableSummaryQuery = $group->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                    $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$group->getId(), $previousRecordGroupKey])) . '.summary';
                    $this->setRenderKey($groupSummaryKey);
                    ?>

                    <?= $this->generateSummaryRowHtml(
                        columns: $this->getVisibleColumns(),
                        heading: $isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                        query: $groupScopedAllTableSummaryQuery,
                        selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                        actions: $this->hasRenderRecordActions(),
                        actionsPosition: $recordActionsPosition,
                        groupColumn: $groupColumn,
                        groupsOnly: $isGroupsOnly,
                        selectionEnabled: $isSelectionEnabled,
                        recordCheckboxPosition: $recordCheckboxPosition,
                        attributes: new ComponentAttributeBag(['wire:key' => $groupSummaryKey]),
                    ); ?>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?php if ((! $isReordering) && $this->hasRenderSummary()) {
                    $groupColumn = $group?->getColumn();
                    ?>

                    <?= $this->generateSummaryHtml(
                        columns: $this->getVisibleColumns(),
                        pluralModelLabel: $this->getPluralModelLabel(),
                        records: $records,
                        actions: $this->hasRenderRecordActions(),
                        actionsPosition: $recordActionsPosition,
                        allTableSummary: $this->hasAllTableSummary(),
                        groupColumn: $groupColumn,
                        groupsOnly: $isGroupsOnly,
                        pageSummary: $this->hasPageSummary(),
                        recordCheckboxPosition: $recordCheckboxPosition,
                        selectionEnabled: $isSelectionEnabled,
                        attributes: new ComponentAttributeBag([]),
                    ); ?>
                <?php } ?>
            </tbody>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateRecordHtml(
        string $recordKey,
        string | Htmlable | null $recordGroupTitle,
        bool $recordIsSelectable,
        ?string $recordAction,
        ?string $recordUrl,
        bool $isRecordRowStriped,
        Model | array $record,
        array $recordActions,
        ?string $recordGroupKey,
        bool $openRecordUrlInNewTab,
        \stdClass $loop,
        bool $isReordering,
        bool $isGroupsOnly,
        bool $isGroupCollapsible,
        string $morphMarkerStartHtml,
        string $morphMarkerEndHtml,
        bool $isSelectionEnabled,
        RecordActionsPosition $recordActionsPosition,
        RecordCheckboxPosition $recordCheckboxPosition,
        bool $isStackedOnMobile,
        Factory $viewFactory,
    ): string {
        ob_start(); ?>
        <?php if (! $isGroupsOnly) { ?>
            <tr
                <?php $this->setRenderKey(e($this->getLivewire()->getId()) . '.table.records.' . e($recordKey)); ?>
                wire:key="<?= e($this->getLivewire()->getId()); ?>.table.records.<?= e($recordKey); ?>"
                <?= e($isReordering ? 'x-sortable-handle' : null); ?>
                <?= $isReordering ? 'x-sortable-item="' . e($recordKey) . '"' : null; ?>
                x-bind:class="{
                    <?= e($isGroupCollapsible ? '\'fi-collapsed\': isGroupCollapsed(' . Js::from($recordGroupTitle) . '),' : ''); ?>
                    'fi-selected': <?= Js::from($recordIsSelectable)->toHtml() ?> && isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>),
                }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-row',
                    'fi-clickable' => $recordAction || $recordUrl,
                    'fi-striped' => $this->isStriped() && $isRecordRowStriped,
                    ...$this->getRecordClasses($record),
                ]); ?>"
            >
                <?= $morphMarkerStartHtml; ?>
                <?php if ($isReordering) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordReorderControlsHtml($record, $recordKey, $loop); ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php if ($this->hasRenderRecordActions() && $recordActionsPosition === RecordActionsPosition::BeforeCells && (! $isReordering)) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions, $viewFactory); ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php if ($isSelectionEnabled && ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) && (! $isReordering)) { ?>
                    <td
                        class="fi-ta-cell fi-ta-selection-cell"
                    >
                        <?php if ($recordIsSelectable) { ?>
                            <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php if ($this->hasRenderRecordActions() && $recordActionsPosition === RecordActionsPosition::BeforeColumns && (! $isReordering)) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions, $viewFactory); ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns(), $viewFactory) as $column) {
                    $loop = $viewFactory->getLastLoop(); ?>
                    <?= $this->generateRecordCellHtml($column, $record, $loop, $recordKey, $recordUrl, $recordAction, $openRecordUrlInNewTab, $isReordering, $isStackedOnMobile); ?>
                <?php } $loop = $viewFactory->getLastLoop(); ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php if ($this->hasRenderRecordActions() && $recordActionsPosition === RecordActionsPosition::AfterColumns && (! $isReordering)) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions, $viewFactory); ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?= $morphMarkerStartHtml; ?>
                <?php if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells && (! $isReordering)) { ?>
                    <td
                        class="fi-ta-cell fi-ta-selection-cell"
                    >
                        <?php if ($recordIsSelectable) { ?>
                            <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?= $morphMarkerEndHtml; ?>

                <?php if ($this->hasRenderRecordActions() && $recordActionsPosition === RecordActionsPosition::AfterCells && (! $isReordering)) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions, $viewFactory); ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        <?php return ob_get_clean();
    }
}
