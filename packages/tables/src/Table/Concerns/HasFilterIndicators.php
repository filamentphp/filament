<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;

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
}
