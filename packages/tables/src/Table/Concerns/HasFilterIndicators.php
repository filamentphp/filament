<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Tables\Filters\BaseFilter;

trait HasFilterIndicators
{
    public function getFilterIndicators(): array
    {
        return [
            ...($this->hasSearch() ? ['resetTableSearch' => $this->getSearchIndicator()] : []),
            ...collect($this->getColumnSearchIndicators())
                ->mapWithKeys(fn (string $indicator, string $column): array => [
                    "resetTableColumnSearch('{$column}')" => $indicator,
                ])
                ->all(),
            ...array_reduce(
                $this->getFilters(),
                fn (array $carry, BaseFilter $filter): array => [
                    ...$carry,
                    ...collect($filter->getIndicators())
                        ->mapWithKeys(fn (string $label, int | string $field) => [
                            "removeTableFilter('{$filter->getName()}'" . (is_string($field) ? ' , \'' . $field . '\'' : null) . ')' => $label,
                        ])
                        ->all(),
                ],
                [],
            ),
        ];
    }
}
