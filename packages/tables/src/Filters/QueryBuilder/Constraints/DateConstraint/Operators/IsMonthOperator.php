<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsMonthOperator extends Operator
{
    public function getName(): string
    {
        return 'isMonth';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is not month' : 'Is month';
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('month')
                ->options($this->getMonths())
                ->required(),
        ];
    }

    public function getSummary(): string
    {
        $month = $this->getMonths()[$this->getSettings()['month']] ?? null;

        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} is not {$month}" : "{$this->getConstraint()->getAttributeLabel()} is {$month}";
    }

    /**
     * @return array<string>
     */
    protected function getMonths(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [
                $month => now()->setMonth($month)->getTranslatedMonthName(),
            ])
            ->all();
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereMonth($qualifiedColumn, $this->isInverse() ? '!=' : '=', $this->getSettings()['month']);
    }
}
