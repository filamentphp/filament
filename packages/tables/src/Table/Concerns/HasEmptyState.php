<?php

namespace Filament\Tables\Table\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Table;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasEmptyState
{
    protected View | Htmlable | Closure | null $emptyState = null;

    protected string | Htmlable | Closure | null $emptyStateDescription = null;

    protected string | Htmlable | Closure | null $emptyStateHeading = null;

    protected string | BackedEnum | Htmlable | Closure | null $emptyStateIcon = null;

    /**
     * @var array<Action | ActionGroup>
     */
    protected array $emptyStateActions = [];

    public function emptyStateDescription(string | Htmlable | Closure | null $description): static
    {
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function emptyState(View | Htmlable | Closure | null $emptyState): static
    {
        $this->emptyState = $emptyState;

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function emptyStateActions(array | ActionGroup $actions, bool $shouldOverwriteExistingActions = false): static
    {
        $this->emptyStateActions = [];
        $this->pushEmptyStateActions($actions, $shouldOverwriteExistingActions);

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushEmptyStateActions(array | ActionGroup $actions, bool $shouldOverwriteExistingActions = false): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedFlatActions($flatActions, $shouldOverwriteExistingActions);
            } elseif ($action instanceof Action) {
                $this->cacheAction($action, $shouldOverwriteExistingActions);
            } else {
                throw new InvalidArgumentException('Table empty state actions must be an instance of [' . Action::class . '] or [' . ActionGroup::class . '].');
            }

            $this->emptyStateActions[] = $action;
        }

        return $this;
    }

    public function emptyStateHeading(string | Htmlable | Closure | null $heading): static
    {
        $this->emptyStateHeading = $heading;

        return $this;
    }

    public function emptyStateIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->emptyStateIcon = $icon;

        return $this;
    }

    public function getEmptyState(): View | Htmlable | null
    {
        return $this->evaluate($this->emptyState);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getEmptyStateActions(): array
    {
        return $this->isReadOnly() ? [] : $this->emptyStateActions;
    }

    public function getEmptyStateDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->emptyStateDescription);
    }

    public function getEmptyStateHeading(): string | Htmlable
    {
        return $this->evaluate($this->emptyStateHeading) ?? __('filament-tables::table.empty.heading', [
            'model' => $this->getPluralModelLabel(),
        ]);
    }

    public function getEmptyStateIcon(): string | BackedEnum | Htmlable
    {
        return $this->evaluate($this->emptyStateIcon)
            ?? FilamentIcon::resolve(TablesIconAlias::EMPTY_STATE)
            ?? Heroicon::OutlinedXMark;
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
}
