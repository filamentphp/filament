<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\ActionGroup;
use Filament\Support\Enums\IconSize;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderContent
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderPagination(): bool
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        return (($records instanceof Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator))
            && (($records instanceof LengthAwarePaginator) ? (bool) $records->total() : $records->isNotEmpty());
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateContentHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;
        $content = $this->getContent();

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if (((! $content) && (! $this->hasColumnsLayout())) || ($records === null) || count($records)) { ?>
            <div
                <?php if ((! $this->isReordering()) && ($pollingInterval = $this->getPollingInterval())) { ?>
                    wire:poll.<?= e($pollingInterval); ?>
                <?php } ?>
                wire:loading.attr="aria-busy"
                <?php if ($this->hasLoadingSkeleton()) { ?>
                    wire:loading.class.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="fi-ta-content-loading"
                <?php } ?>
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                class="fi-ta-content-ctn fi-fixed-positioning-context"
            >
                <?= $this->generateResultCountHtml(); ?>

                <?php if (($content || $this->hasColumnsLayout()) && ($records !== null) && count($records)) { ?>
                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?= $this->generateContentHeaderHtml(); ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?php if ($content) { ?>
                        <?= $this->generateCustomContentHtml($content, ['records' => $records]); ?>
                    <?php } else { ?>
                        <?= $this->generateGridRecordsHtml(); ?>
                    <?php } ?>

                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?php if (($content || $this->hasColumnsLayout()) && ($contentFooter = $this->getContentFooter())) { ?>
                        <?= $this->generateCustomContentHtml($contentFooter, [
                            'columns' => $this->getVisibleColumns(),
                            'records' => $records,
                        ]); ?>
                    <?php } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?php if ((! $this->isReordering()) && ($this->hasPageSummary() || $this->hasAllTableSummary()) && $this->hasRenderSummary()) { ?>
                        <table
                            class="<?= Arr::toCssClasses([
                                'fi-ta-table',
                                'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                            ]); ?>"
                        >
                            <tbody>
                                <?= $this->generateSummaryHtml(
                                    columns: $this->getVisibleColumns(),
                                    pluralModelLabel: $this->getPluralModelLabel(),
                                    records: $records,
                                    allTableSummary: $this->hasAllTableSummary(),
                                    extraHeadingColumn: true,
                                    pageSummary: $this->hasPageSummary(),
                                    placeholderColumns: false,
                                    attributes: new ComponentAttributeBag([]),
                                ); ?>
                            </tbody>
                        </table>
                    <?php } ?>
                <?php } elseif ((! ($content || $this->hasColumnsLayout())) && ($records !== null)) {
                    $sortableColumns = $this->isStackedOnMobile() ? array_filter(
                        $this->getVisibleColumns(),
                        fn (Column $column): bool => $column->isSortable(),
                    ) : [];
                    ?>

                    <?= $this->generateRecordsTableHtml($sortableColumns); ?>
                <?php } elseif ($records === null) { ?>
                    <div
                        role="status"
                        aria-busy="true"
                        aria-live="polite"
                        class="fi-ta-table-loading-ctn"
                    >
                        <span class="fi-sr-only">
                            <?= e(__('filament-tables::table.loading')); ?>
                        </span>

                        <div aria-hidden="true" class="fi-loading-placeholder-content">
                            <?php if (($loadingPlaceholder = $this->getLoadingPlaceholder()) !== null) { ?>
                                <?= e($loadingPlaceholder); ?>
                            <?php } else { ?>
                                <?= view('filament::components.loading-placeholder', [
                                    'loadingLabel' => __('filament-tables::table.loading'),
                                    'attributes' => new ComponentAttributeBag,
                                    'slot' => new ComponentSlot,
                                ])->render(); ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
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
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateCustomContentHtml(View | Htmlable $content, array $data): string
    {
        $content = $content->with($data);

        return $content instanceof Htmlable ? $content->toHtml() : $content->render();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateContentHeaderHtml(): string
    {
        ob_start();

        if (! $this->isReordering()) {
            $sortableColumns = array_filter(
                $this->getVisibleColumns(),
                fn (Column $column): bool => $column->isSortable(),
            );

            if (($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) || count($sortableColumns)) { ?>
                <div class="fi-ta-content-header">
                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?php if ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) { ?>
                        <?= $this->generatePageCheckboxHtml(); ?>
                    <?php } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?php if (count($sortableColumns)) { ?>
                        <div
                            x-data="filamentTableSort"
                            class="fi-ta-sorting-settings"
                        >
                            <label>
                                <?php $inputWrapperArguments = ['prefix' => __('filament-tables::table.sorting.fields.column.label'), 'attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'column'])];
                        ob_start(); ?>
                                        <option value="">
                                            <?= e($this->getDefaultSortOptionLabel()); ?>
                                        </option>

                                        <?php foreach ($this->iterateRenderLoop($sortableColumns) as $column) { ?>
                                            <option
                                                value="<?= e($column->getName()); ?>"
                                            >
                                                <?= e($column->getLabel()); ?>
                                            </option>
                                        <?php } ?>
                                    <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>

                            <label x-cloak x-show="column">
                                <span class="fi-sr-only">
                                    <?= e(__('filament-tables::table.sorting.fields.direction.label')); ?>
                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                        ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.asc')); ?>
                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.desc')); ?>
                                        </option>
                                    <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateEmptyStateHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if (($records !== null) && ! count($records)) { ?>
            <?php if ($emptyState = $this->getEmptyState()) { ?>
                <?= e($emptyState); ?>
            <?php } else { ?>
                <div class="fi-ta-empty-state" role="status">
                    <div class="fi-ta-empty-state-content">
                        <div class="fi-ta-empty-state-icon-bg">
                            <?= e(\Filament\Support\generate_icon_html($this->getEmptyStateIcon(), attributes: (new FilamentComponentAttributeBag([
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => implode(',', Table::LOADING_TARGETS),
                            ])), size: IconSize::Large)); ?>

                            <?= e(\Filament\Support\generate_loading_indicator_html((new FilamentComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => implode(',', Table::LOADING_TARGETS),
                            ])), size: IconSize::Large)); ?>
                        </div>

                        <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>
                            class="fi-ta-empty-state-heading"
                        >
                            <?= e($this->getEmptyStateHeading()); ?>
                        </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                        <?= $this->generateMorphMarkerHtml(); ?>
                        <?php if (filled($emptyStateDescription = $this->getEmptyStateDescription())) { ?>
                            <p class="fi-ta-empty-state-description">
                                <?= e($emptyStateDescription); ?>
                            </p>
                        <?php } ?>
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                        <?php if ($emptyStateActions = array_filter(
                            $this->getEmptyStateActions(),
                            fn (\Filament\Actions\Action | ActionGroup $action): bool => $action->isVisible(),
                        )) { ?>
                            <div
                                class="fi-ta-actions fi-align-center fi-wrapped"
                            >
                                <?php foreach ($this->iterateRenderLoop($emptyStateActions) as $action) { ?>
                                    <?= e($action); ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generatePaginationContainerHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->hasRenderPagination()) {
            $hasExtremePaginationLinks = $this->hasExtremePaginationLinks();
            $paginationPageOptions = $this->getPaginationPageOptions();
            ?>

            <?= ($records instanceof AbstractPaginator) || ($records instanceof CursorPaginator)
                ? $this->generatePaginationHtml(
                    paginator: $records,
                    livewireId: $this->getLivewire()->getId(),
                    extremeLinks: $hasExtremePaginationLinks,
                    pageOptions: $paginationPageOptions,
                    loadingTargets: Table::LOADING_TARGETS,
                    isRtl: __('filament-panels::layout.direction') === 'rtl',
                )
                : view('filament::components.pagination.index', [
                    'paginator' => $records,
                    'extremeLinks' => $hasExtremePaginationLinks,
                    'pageOptions' => $paginationPageOptions,
                    'loadingTargets' => Table::LOADING_TARGETS,
                    'attributes' => new ComponentAttributeBag,
                ])->render(); ?>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }
}
