<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait HasBulkActions
{
    protected ?Closure $checkIfRecordIsSelectableUsing = null;

    protected ?Closure $modifySelectableRecordsQueryUsing = null;

    protected bool | Closure | null $selectsCurrentPageOnly = false;

    protected bool | Closure | null $selectsGroupsOnly = false;

    protected RecordCheckboxPosition | Closure | null $recordCheckboxPosition = null;

    protected bool | Closure | null $isSelectable = null;

    protected bool | Closure $canTrackDeselectedRecords = true;

    protected string | Closure | null $currentSelectionLivewireProperty = null;

    protected int | Closure | null $maxSelectableRecords = null;

    protected bool | Closure $isSelectionDisabled = false;

    protected ?int $cachedRenderAllSelectableRecordsCount = null;

    /**
     * @deprecated Use `toolbarActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function bulkActions(array | ActionGroup $actions): static
    {
        $this->toolbarActions($actions);

        return $this;
    }

    /**
     * @deprecated Use `pushToolbarActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushBulkActions(array | ActionGroup $actions): static
    {
        $this->pushToolbarActions($actions);

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    public function groupedBulkActions(array $actions): static
    {
        $this->toolbarActions([BulkActionGroup::make($actions)]);

        return $this;
    }

    public function checkIfRecordIsSelectableUsing(?Closure $callback): static
    {
        $this->checkIfRecordIsSelectableUsing = $callback;

        return $this;
    }

    public function selectableRecordsQuery(?Closure $callback): static
    {
        $this->modifySelectableRecordsQueryUsing = $callback;

        return $this;
    }

    public function selectCurrentPageOnly(bool | Closure $condition = true): static
    {
        $this->selectsCurrentPageOnly = $condition;

        return $this;
    }

    public function selectGroupsOnly(bool | Closure $condition = true): static
    {
        $this->selectsGroupsOnly = $condition;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function isRecordSelectable(Model | array $record): bool
    {
        if (($record instanceof Model) && $this->hasSelectableRecordsQuery() && (! $this->getLivewire()->isTableRecordSelectableUsingQuery($record))) {
            return false;
        }

        return $this->isRecordSelectableUsingCallback($record);
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function isRecordSelectableUsingCallback(Model | array $record): bool
    {
        return (bool) ($this->evaluate(
            $this->checkIfRecordIsSelectableUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        ) ?? true);
    }

    public function getAllSelectableRecordsCount(): int
    {
        return $this->getLivewire()->getAllSelectableTableRecordsCount();
    }

    public function selectable(bool | Closure | null $condition = true): static
    {
        $this->isSelectable = $condition;

        return $this;
    }

    public function isSelectionEnabled(): bool
    {
        if (is_bool($isSelectable = $this->evaluate($this->isSelectable))) {
            return $isSelectable;
        }

        foreach ($this->getFlatBulkActions() as $action) {
            if ($action->isVisible()) {
                return true;
            }
        }

        return false;
    }

    public function selectsCurrentPageOnly(): bool
    {
        return $this->evaluate($this->selectsCurrentPageOnly) || (! $this->hasQuery());
    }

    public function selectsGroupsOnly(): bool
    {
        return (bool) $this->evaluate($this->selectsGroupsOnly);
    }

    public function checksIfRecordIsSelectable(): bool
    {
        return $this->checkIfRecordIsSelectableUsing !== null;
    }

    public function hasSelectableRecordsQuery(): bool
    {
        return $this->modifySelectableRecordsQueryUsing !== null;
    }

    public function modifySelectableRecordsQuery(Builder $query): Builder
    {
        if (! $this->hasSelectableRecordsQuery()) {
            return $query;
        }

        return $query->where(function (Builder $query): void {
            $this->evaluate($this->modifySelectableRecordsQueryUsing, [
                'query' => $query,
            ]);
        });
    }

    public function recordCheckboxPosition(RecordCheckboxPosition | Closure | null $position = null): static
    {
        $this->recordCheckboxPosition = $position;

        return $this;
    }

    public function getRecordCheckboxPosition(): RecordCheckboxPosition
    {
        return $this->evaluate($this->recordCheckboxPosition) ?? RecordCheckboxPosition::BeforeCells;
    }

    public function trackDeselectedRecords(bool | Closure $condition = true): static
    {
        $this->canTrackDeselectedRecords = $condition;

        return $this;
    }

    public function canTrackDeselectedRecords(): bool
    {
        if (! $this->shouldDeselectAllRecordsWhenFiltered()) {
            return false;
        }

        return (bool) $this->evaluate($this->canTrackDeselectedRecords);
    }

    public function currentSelectionLivewireProperty(string | Closure | null $property): static
    {
        $this->currentSelectionLivewireProperty = $property;

        return $this;
    }

    public function getCurrentSelectionLivewireProperty(): ?string
    {
        return $this->evaluate($this->currentSelectionLivewireProperty);
    }

    public function maxSelectableRecords(int | Closure | null $count): static
    {
        $this->maxSelectableRecords = $count;

        return $this;
    }

    public function getMaxSelectableRecords(): ?int
    {
        return $this->evaluate($this->maxSelectableRecords);
    }

    public function disabledSelection(bool | Closure $condition = true): static
    {
        $this->isSelectionDisabled = $condition;

        return $this;
    }

    public function isSelectionDisabled(): bool
    {
        return (bool) $this->evaluate($this->isSelectionDisabled);
    }

    /**
     * @deprecated Use `getToolbarActions()` instead.
     *
     * @return array<Action | ActionGroup>
     */
    public function getBulkActions(): array
    {
        return $this->getToolbarActions();
    }

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
            <?php $this->setRenderKey(e($this->getLivewire()->getId()) . '.table.selection.indicator'); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.selection.indicator"
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
                        <?= $this->generateMorphMarkerHtml(); ?>
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
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

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
        $checkboxKey = e($this->getLivewire()->getId()) . '.table.bulk-select-page.checkbox.' . ($isStacked ? 'stacked.' : '') . e(Str::random());

        ob_start(); ?>
        <input
            aria-label="<?= e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
            type="checkbox"
            autocomplete="off"
            <?php if ($this->isSelectionDisabled()) { ?>
                disabled
            <?php } elseif ($this->getMaxSelectableRecords()) { ?>
                x-bind:disabled="shouldDisableSelectRecordsOnPageCheckbox()"
            <?php } ?>
            x-bind:checked="syncSelectRecordsOnPageCheckbox($el)"
            x-on:click="toggleSelectRecordsOnPage"
            <?php $this->setRenderKey($checkboxKey); ?>
            wire:key="<?= $checkboxKey; ?>"
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
            autocomplete="off"
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
            <?php $this->setRenderKey(e($this->getLivewire()->getId()) . '.table.bulk_select_group.checkbox.' . e($livewire->getTablePage())); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.bulk_select_group.checkbox.<?= e($livewire->getTablePage()); ?>"
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
            autocomplete="off"
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
