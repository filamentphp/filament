<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class IsDateOperator extends Operator
{
    public function getName(): string
    {
        return 'isDate';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is not date' : 'Is date';
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            DatePicker::make('date')
                ->required(),
        ];
    }

    public function getSummary(): string
    {
        $date = Carbon::parse($this->getSettings()['date'])->toFormattedDateString();

        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} is not {$date}" : "{$this->getConstraint()->getAttributeLabel()} is {$date}";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '!=' : '=', $this->getSettings()['date']);
    }
}
