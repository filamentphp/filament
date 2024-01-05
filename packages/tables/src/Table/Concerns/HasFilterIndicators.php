<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;

trait HasFilterIndicators
{
    protected ?bool $hideFilterIndicators = null;

    /**
     * @return array<Indicator>
     */
    public function getFilterIndicators(): array
    {
        if ($this->hideFilterIndicators) {
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

    public function hideFilterIndicators(?bool $condition = true): static
    {
        $this->hideFilterIndicators = $condition;

        return $this;
    }
}
