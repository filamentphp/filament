<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait HasFilterIndicators
{
    protected bool | Closure $areFilterIndicatorsHidden = false;

    /**
     * @return array<Indicator>
     */
    public function getFilterIndicators(): array
    {
        if ($this->evaluate($this->areFilterIndicatorsHidden)) {
            return [];
        }

        return [
            ...($this->hasSearch() ? [$this->getSearchIndicator()] : []),
            ...$this->getColumnSearchIndicators(),
            ...array_reduce(
                $this->getFilters(),
                fn (array $carry, BaseFilter $filter): array => [
                    ...$carry,
                    ...collect($filter->getIndicators())
                        ->map(function (Indicator $indicator) use ($filter): Indicator {
                            $removeField = $indicator->getRemoveField();

                            return $indicator->removeLivewireClickHandler("removeTableFilter('{$filter->getName()}'" . (filled($removeField) ? ', \'' . $removeField . '\'' : null) . ')');
                        })
                        ->all(),
                ],
                [],
            ),
        ];
    }

    public function hiddenFilterIndicators(bool | Closure $condition = true): static
    {
        $this->areFilterIndicatorsHidden = $condition;

        return $this;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFilterIndicatorsHtml(): string
    {
        $livewire = $this->getLivewire();
        $filterIndicators = $this->getFilterIndicators();

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($filterIndicators) { ?>
            <?php if (filled($filterIndicatorsView = FilamentView::renderHook(TablesRenderHook::FILTER_INDICATORS, scopes: $livewire::class, data: ['filterIndicators' => $filterIndicators]))) { ?>
                <?= e($filterIndicatorsView); ?>
            <?php } else { ?>
                <div class="fi-ta-filter-indicators">
                    <div>
                        <span class="fi-ta-filter-indicators-label">
                            <?= e(__('filament-tables::table.filters.indicator')); ?>
                        </span>

                        <div
                            class="fi-ta-filter-indicators-badges-ctn"
                            role="list"
                        >
                            <?php foreach ($this->iterateRenderLoop($filterIndicators) as $indicatorIndex => $indicator) {
                                $this->setRenderKey($livewire->getId() . '.table.filter-indicator.' . $indicatorIndex); ?>
                                <?= $this->generateFilterIndicatorHtml($indicator); ?>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (collect($filterIndicators)->contains(fn (Indicator $indicator): bool => $indicator->isRemovable())) { ?>
                        <?= e($this->getFiltersRemoveAllAction()); ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFilterIndicatorHtml(Indicator $indicator): string
    {
        $color = $indicator->getColor();
        $slot = new ComponentSlot(trim(e($indicator->getLabel())));
        $deleteButton = null;

        if ($indicator->isRemovable()) {
            $removeLivewireClickHandler = $indicator->getRemoveLivewireClickHandler();
            $deleteButton = new ComponentSlot('', [
                'label' => BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.filters.actions.remove.label')),
                'wire:click' => BladeCompiler::sanitizeComponentAttribute($removeLivewireClickHandler),
                'wire:target' => 'removeTableFilter',
            ]);
        }

        return $this->generateBadgeHtml(
            label: $slot,
            attributes: new ComponentAttributeBag(['role' => 'listitem']),
            color: $color,
            deleteButton: $deleteButton,
            size: Size::Medium,
            shouldCheckForPublishedView: true,
        );
    }
}
