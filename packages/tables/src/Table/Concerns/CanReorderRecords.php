<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

trait CanReorderRecords
{
    use HasReorderAnimationDuration;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderAuthorized = true;

    protected ?bool $cachedIsReorderAuthorized = null;

    protected string | Closure | null $reorderColumn = null;

    protected string | Closure | null $reorderDirection = null;

    protected ?Closure $modifyReorderRecordsTriggerActionUsing = null;

    protected ?Closure $beforeReorderingCallback = null;

    protected ?Closure $afterReorderingCallback = null;

    public function reorderRecordsTriggerAction(?Closure $callback): static
    {
        $this->modifyReorderRecordsTriggerActionUsing = $callback;

        return $this;
    }

    public function reorderable(string | Closure | null $column = null, bool | Closure | null $condition = null, string | Closure | null $direction = null): static
    {
        $this->reorderColumn = $column;

        if ($condition !== null) {
            $this->isReorderable = $condition;
        }

        $this->reorderDirection = $direction;

        return $this;
    }

    public function beforeReordering(?Closure $callback): static
    {
        $this->beforeReorderingCallback = $callback;

        return $this;
    }

    public function afterReordering(?Closure $callback): static
    {
        $this->afterReorderingCallback = $callback;

        return $this;
    }

    public function authorizeReorder(bool | Closure $condition = true): static
    {
        $this->isReorderAuthorized = $condition;
        $this->cachedIsReorderAuthorized = null;

        return $this;
    }

    public function getReorderRecordsTriggerAction(bool $isReordering): Action
    {
        $action = Action::make('reorderRecords')
            ->label($isReordering ? __('filament-tables::table.actions.disable_reordering.label') : __('filament-tables::table.actions.enable_reordering.label'))
            ->iconButton()
            ->icon($isReordering ? (FilamentIcon::resolve(TablesIconAlias::ACTIONS_DISABLE_REORDERING) ?? Heroicon::Check) : (FilamentIcon::resolve(TablesIconAlias::ACTIONS_ENABLE_REORDERING) ?? Heroicon::ArrowsUpDown))
            ->color('gray')
            ->action('toggleTableReordering')
            ->table($this)
            ->authorize(true);

        if ($this->modifyReorderRecordsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyReorderRecordsTriggerActionUsing, [
                'action' => $action,
                'isReordering' => $isReordering,
            ]) ?? $action;
        }

        $action->extraAttributes(['class' => 'fi-force-enabled'], merge: true);

        return $action;
    }

    public function getReorderColumn(): ?string
    {
        return $this->evaluate($this->reorderColumn);
    }

    public function getReorderDirection(): string
    {
        return $this->evaluate($this->reorderDirection) ?? 'asc';
    }

    public function isReorderable(): bool
    {
        return filled($this->getReorderColumn()) && $this->evaluate($this->isReorderable) && $this->isReorderAuthorized();
    }

    public function isReordering(): bool
    {
        return $this->getLivewire()->isTableReordering();
    }

    public function isReorderAuthorized(): bool
    {
        return $this->cachedIsReorderAuthorized ??= (bool) $this->evaluate($this->isReorderAuthorized);
    }

    /**
     * @param  array<int | string>  $order
     */
    public function callBeforeReordering(array $order): void
    {
        $this->evaluate($this->beforeReorderingCallback, ['order' => $order]);
    }

    /**
     * @param  array<int | string>  $order
     */
    public function callAfterReordering(array $order): void
    {
        $this->evaluate($this->afterReorderingCallback, ['order' => $order]);
    }

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
