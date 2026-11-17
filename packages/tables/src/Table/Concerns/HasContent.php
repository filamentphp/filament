<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait HasContent
{
    protected View | Htmlable | Closure | null $content = null;

    protected View | Htmlable | Closure | null $contentFooter = null;

    /**
     * @var array<string, int | null> | Closure | null
     */
    protected array | Closure | null $contentGrid = null;

    public function content(View | Htmlable | Closure | null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function contentFooter(View | Htmlable | Closure | null $footer): static
    {
        $this->contentFooter = $footer;

        return $this;
    }

    /**
     * @param  array<string, int | null> | Closure | null  $grid
     */
    public function contentGrid(array | Closure | null $grid): static
    {
        $this->contentGrid = $grid;

        return $this;
    }

    public function getContent(): View | Htmlable | null
    {
        return $this->evaluate($this->content);
    }

    /**
     * @return array<string, int | null> | null
     */
    public function getContentGrid(): ?array
    {
        return $this->evaluate($this->contentGrid);
    }

    public function getContentFooter(): View | Htmlable | null
    {
        return $this->evaluate($this->contentFooter);
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
}
