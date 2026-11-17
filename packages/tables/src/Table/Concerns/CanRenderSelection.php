<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderSelection
{
    protected ?int $cachedRenderAllSelectableRecordsCount = null;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function isRenderSelectionEnabled(): bool
    {
        return (! $this->isRenderGroupsOnly()) && $this->isSelectionEnabled();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderAllSelectableRecordsCount(): ?int
    {
        if ((! $this->isRenderSelectionEnabled()) || (! $this->isLoaded())) {
            return null;
        }

        if ($this->cachedRenderAllSelectableRecordsCount !== null) {
            return $this->cachedRenderAllSelectableRecordsCount;
        }

        // The Livewire count may use its cached records directly.
        $this->getRecords();

        return $this->cachedRenderAllSelectableRecordsCount = $this->getAllSelectableRecordsCount();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSelectionIndicatorHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div
            x-cloak
            role="status"
            aria-live="polite"
            aria-atomic="true"
            x-bind:hidden="! getSelectedRecordsCount()"
            x-show="getSelectedRecordsCount()"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.selection.indicator'); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.selection.indicator"
            class="fi-ta-selection-indicator"
        >
            <div>
                <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                    'x-show' => 'isLoading',
                ]))); ?>

                <span
                    x-text="getSelectedRecordsCountLabel(<?= Js::from(__('filament-tables::table.selection_indicator.selected_count'))->toHtml() ?>, <?= Js::from(str_replace('_', '-', app()->getLocale()))->toHtml() ?>)"
                ></span>
            </div>

            <?php if (! $this->isSelectionDisabled()) { ?>
                <div>
                    <?= e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: $livewire::class)); ?>

                    <div class="fi-ta-selection-indicator-actions-ctn">
                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if (! $this->selectsGroupsOnly()) { ?>
                            <?php $linkArguments = [
                                'color' => 'primary',
                                'tag' => 'button',
                                'attributes' => new ComponentAttributeBag([
                                    'x-on:click' => 'selectAllRecords',
                                    'x-show' => 'canSelectAllRecords()',
                                    'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . 'table.selection.indicator.actions.select-all.' . $this->getRenderAllSelectableRecordsCount() . '.' . $livewire->getTablePage()),
                                ]),
                            ];
                            ob_start(); ?>
                                <?= e(trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $this->getRenderAllSelectableRecordsCount(), ['count' => Number::format($this->getRenderAllSelectableRecordsCount(), locale: app()->getLocale())])); ?>

                             <?= $this->generateLinkHtml(...$linkArguments, label: new ComponentSlot(trim(ob_get_clean())), shouldCheckForPublishedView: true); ?>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?php $linkArguments = ['color' => 'danger', 'tag' => 'button', 'attributes' => new ComponentAttributeBag(['x-on:click' => 'deselectAllRecords'])];
                ob_start(); ?>
                            <?= e(__('filament-tables::table.selection_indicator.actions.deselect_all.label')); ?>

                         <?= $this->generateLinkHtml(...$linkArguments, label: new ComponentSlot(trim(ob_get_clean())), shouldCheckForPublishedView: true); ?>
                    </div>

                    <?= e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER, scopes: $livewire::class)); ?>

                </div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generatePageCheckboxHtml(bool $isStacked = false): string
    {
        ob_start(); ?>
        <input
            aria-label="<?= e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
            type="checkbox"
            <?php if ($this->isSelectionDisabled()) { ?>
                disabled
            <?php } elseif ($this->getMaxSelectableRecords()) { ?>
                x-bind:disabled="shouldDisableSelectRecordsOnPageCheckbox()"
            <?php } ?>
            x-bind:checked="syncSelectRecordsOnPageCheckbox($el)"
            x-on:click="toggleSelectRecordsOnPage"

            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.bulk-select-page.checkbox.' . ($isStacked ? 'stacked.' : '') . e(Str::random()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.bulk-select-page.checkbox.<?= $isStacked ? 'stacked.' : ''; ?><?= e(Str::random()); ?>"
            wire:loading.attr="disabled"
            wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            class="fi-ta-page-checkbox fi-checkbox-input"
        />
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGroupCheckboxHtml(string | Htmlable | null $recordGroupTitle, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <input
                    aria-label="<?= e(__('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle])); ?>"
                    type="checkbox"
                    data-group-selectable-record-keys="<?= e(json_encode($livewire->getGroupedSelectableTableRecordKeys($recordGroupKey))); ?>"
                    <?php if ($this->isSelectionDisabled()) { ?>
                        disabled
                    <?php } else { ?>
                        x-on:click="toggleSelectGroupRecords($el)"
                        <?php if ($this->getMaxSelectableRecords()) { ?>
                            x-bind:disabled="shouldDisableSelectGroupRecordsCheckbox($el)"
                        <?php } ?>
                    <?php } ?>
                    x-bind:checked="syncSelectGroupRecordsCheckbox($el)"
                    <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.bulk_select_group.checkbox.' . e($livewire->getTablePage()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.bulk_select_group.checkbox.<?= e($livewire->getTablePage()); ?>"
                    wire:loading.attr="disabled"
                    wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                    class="fi-ta-group-checkbox fi-checkbox-input"
                />
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateRecordCheckboxHtml(string $recordKey, ?string $recordGroupKey): string
    {
        ob_start(); ?>
        <input
            aria-label="<?= e(__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])); ?>"
            type="checkbox"
            <?php if ($this->isSelectionDisabled()) { ?>
                disabled
            <?php } elseif ($this->getMaxSelectableRecords() && ($this->getMaxSelectableRecords() !== 1)) { ?>
                x-bind:disabled="! areRecordsToggleable([<?= Js::from($recordKey)->toHtml() ?>])"
            <?php } ?>
            value="<?= e($recordKey); ?>"
            x-on:click="toggleSelectedRecord(<?= Js::from($recordKey)->toHtml() ?>)"
            x-bind:checked="isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>) ? 'checked' : null"
            data-group="<?= e($recordGroupKey); ?>"
            wire:loading.attr="disabled"
            wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            class="fi-ta-record-checkbox fi-checkbox-input"
        />
        <?php return ob_get_clean();
    }
}
