<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderSearch
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGlobalSearchHtml(): string
    {
        ob_start();

        if ($this->isSearchable()) {
            $searchPlaceholder = $this->getSearchPlaceholder();
            ?>

            <?= $this->generateSearchFieldHtml(
                debounce: $this->getSearchDebounce(),
                onBlur: $this->isSearchOnBlur(),
                placeholder: $searchPlaceholder,
                attributes: new ComponentAttributeBag([]),
            ); ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnSearchesHtml(): string
    {
        $records = $this->getRecords();

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->isSearchableByColumn()) { ?>
            <tbody>
            <?php
            $breakpointOrder = [
                'base' => 0,
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ];
            $responsiveBreakpointOrder = array_diff_key($breakpointOrder, ['base' => true]);
            $individualSearchHiddenAt = [];

            $individualSearchColumnStates = [];

            foreach ($this->getVisibleColumns() as $columnKey => $column) {
                $individualSearchColumnStates[$columnKey] = [
                    'hiddenFrom' => $column->getHiddenFrom(),
                    'isIndividuallySearchable' => $column->isIndividuallySearchable(),
                    'visibleFrom' => $column->getVisibleFrom(),
                ];
            }

            foreach ($breakpointOrder as $breakpoint => $breakpointIndex) {
                foreach ($individualSearchColumnStates as $columnState) {
                    if (! $columnState['isIndividuallySearchable']) {
                        continue;
                    }

                    $visibleFromIndex = filled($columnState['visibleFrom']) ? ($responsiveBreakpointOrder[$columnState['visibleFrom']] ?? null) : null;

                    if ($visibleFromIndex !== null) {
                        if ($breakpointIndex >= $visibleFromIndex) {
                            continue 2;
                        }

                        continue;
                    }

                    $hiddenFromIndex = filled($columnState['hiddenFrom']) ? ($responsiveBreakpointOrder[$columnState['hiddenFrom']] ?? null) : null;

                    if (($hiddenFromIndex === null) || ($breakpointIndex < $hiddenFromIndex)) {
                        continue 2;
                    }
                }

                $individualSearchHiddenAt[] = $breakpoint;
            }
            ?>

                <tr
                    <?php if (filled($individualSearchHiddenAt)) { ?>
                        data-search-hidden-at="<?= e(implode(' ', $individualSearchHiddenAt)); ?>"
                    <?php } ?>
                    class="fi-ta-row fi-ta-row-not-reorderable fi-ta-individual-search-row"
                >
                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?php if (count($records)) { ?>
                        <?php if ($this->isReordering()) { ?>
                            <td></td>
                        <?php } else { ?>
                            <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])) { ?>
                                <td></td>
                            <?php } ?>

                            <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                                <td></td>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $columnKey => $column) {
                        $columnName = $column->getName();
                        $columnState = $individualSearchColumnStates[$columnKey];
                        $isIndividuallySearchable = $columnState['isIndividuallySearchable'];
                        $columnHiddenFrom = $columnState['hiddenFrom'];
                        $columnVisibleFrom = $columnState['visibleFrom'];
                        ?>

                        <td
                            class="<?= Arr::toCssClasses([
                                'fi-ta-cell',
                                'fi-ta-individual-search-cell' => $isIndividuallySearchable,
                                'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                                filled($columnHiddenFrom) ? "{$columnHiddenFrom}:fi-hidden" : '',
                                filled($columnVisibleFrom) ? "{$columnVisibleFrom}:fi-visible" : '',
                            ]); ?>"
                        >
                            <?php if ($isIndividuallySearchable) { ?>
                                <?= $this->generateSearchFieldHtml(
                                    debounce: $this->getSearchDebounce(),
                                    label: $column->getLabel(),
                                    labelHidden: ! $this->isStackedOnMobile(),
                                    onBlur: $this->isSearchOnBlur(),
                                    wireModel: 'tableColumnSearches.' . $columnName,
                                    attributes: new ComponentAttributeBag([]),
                                ); ?>
                            <?php } ?>
                        </td>
                    <?php } ?>

                    <?php if ((! $this->isReordering()) && count($records)) { ?>
                        <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) { ?>
                            <td></td>
                        <?php } ?>

                        <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                            <td></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            </tbody>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSearchFieldHtml(
        string | int $debounce = '500ms',
        string | Htmlable | null $label = null,
        bool $labelHidden = true,
        bool $onBlur = false,
        ?string $placeholder = null,
        string $wireModel = 'tableSearch',
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;
        $label ??= __('filament-tables::table.fields.search.label');
        $placeholder ??= __('filament-tables::table.fields.search.placeholder');
        $livewire = $this->getLivewire();

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.search-field')) {
            return view('filament-tables::components.search-field', [
                'debounce' => $debounce,
                'label' => $label,
                'labelHidden' => $labelHidden,
                'onBlur' => $onBlur,
                'placeholder' => $placeholder,
                'wireModel' => $wireModel,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $livewire,
            ])->render();
        }

        $wireModelAttribute = $onBlur ? 'wire:model.blur' : "wire:model.live.debounce.{$debounce}";
        $inputKey = $livewire->getId() . '.table.' . $wireModel . '.field.input';
        $this->setRenderKey($inputKey);

        $inputHtml = $this->generateInputHtml(
            attributes: (new ComponentAttributeBag)->merge([
                'autocomplete' => 'off',
                'maxlength' => 1000,
                'placeholder' => $placeholder,
                'type' => 'search',
                'wire:key' => $inputKey,
                $wireModelAttribute => $wireModel,
                'x-bind:id' => '$id(\'input\')',
                'x-on:keyup' => '$event.key === \'Enter\' && $wire.$refresh()',
            ], escape: false),
            inlinePrefix: true,
        );

        return '<div x-id="[\'input\']" ' . $attributes->class(['fi-ta-search-field'])->toHtml() . '>'
            . '<label x-bind:for="$id(\'input\')" class="' . ($labelHidden ? 'fi-sr-only' : 'fi-ta-cell-label') . '">' . e($label) . '</label>'
            . $this->generateInputWrapperHtml(
                attributes: new FilamentComponentAttributeBag(['wire:target' => $wireModel]),
                slot: new HtmlString($inputHtml),
                inlinePrefix: true,
                prefixIcon: Heroicon::MagnifyingGlass,
                prefixIconAlias: TablesIconAlias::SEARCH_FIELD,
            )
            . '</div>';
    }
}
