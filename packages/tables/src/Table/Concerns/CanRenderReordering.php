<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

trait CanRenderReordering
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateReorderingIndicatorHtml(): string
    {
        ob_start(); ?>
        <div
            x-cloak
            role="status"
            aria-live="polite"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.reorder.indicator'); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.reorder.indicator"
            class="fi-ta-reorder-indicator"
        >
            <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => 'reorderTable,moveTableRecord',
            ]))); ?>

            <?= e(__('filament-tables::table.reorder_indicator')); ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateRecordReorderControlsHtml(Model | array $record, string $recordKey, \stdClass $loop): string
    {
        $viewFactory = app('view');

        ob_start();

        $reorderLabel = ($record instanceof Model && ($this->hasCustomRecordTitle() || filled($this->getRecordTitleAttribute()))) ? $this->getRecordTitle($record) : $recordKey;
        $reorderPosition = $loop->iteration;
        $reorderCount = $loop->count;
        $reorderDescriptionId = $this->getLivewire()->getId() . '-table-reorder-' . md5($recordKey);
        ?>

        <button
            aria-hidden="true"
            tabindex="-1"
            class="fi-ta-reorder-handle fi-icon-btn"
            type="button"
        >
            <?= e(\Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE)); ?>
        </button>

        <span
            id="<?= e($reorderDescriptionId); ?>"
            class="fi-sr-only"
            data-table-reorder-label="<?= e($reorderLabel); ?>"
        >
            <?= e(__('filament-tables::table.reordering.position', ['label' => $reorderLabel, 'position' => $reorderPosition, 'count' => $reorderCount])); ?>
        </span>

        <div class="fi-sr-only">
            <?php foreach ($this->iterateRenderLoop(['up', 'down']) as $direction) {
                $loop = $viewFactory->getLastLoop(); ?>
                <button
                    type="button"
                    aria-describedby="<?= e($reorderDescriptionId); ?>"
                    <?php if (($direction === 'up') ? ($reorderPosition === 1) : ($reorderPosition === $reorderCount)) {
                        echo 'disabled';
                    } ?>
                    tabindex="-1"
                    data-table-reorder-direction="<?= e($direction); ?>"
                    x-bind:aria-disabled="tableReorderOperation !== null || $el.disabled"
                    x-on:click.stop="moveTableRecord($event, <?= Js::from($recordKey)->toHtml() ?>, <?= Js::from($direction)->toHtml() ?>)"
                >
                    <?= e(__('filament-tables::table.actions.move_record_' . $direction . '.label', ['label' => $reorderLabel])); ?>
                </button>
            <?php } $loop = $viewFactory->getLastLoop(); ?>
        </div>
        <?php return ob_get_clean();
    }
}
