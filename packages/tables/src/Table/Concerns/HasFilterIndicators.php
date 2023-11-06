<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;

trait HasFilterIndicators
{
    /**
     * @return array<Indicator>
     */
    public function getFilterIndicators(): array
    {
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
}
