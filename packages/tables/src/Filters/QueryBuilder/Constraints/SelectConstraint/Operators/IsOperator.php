<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators;

use Exception;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class IsOperator extends Operator
{
    public function getName(): string
    {
        return 'is';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Is not' : 'Is';
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        $constraint = $this->getConstraint();

        $field = Select::make($constraint->isMultiple() ? 'values' : 'value')
            ->label($constraint->isMultiple() ? 'Values' : 'Value')
            ->options($constraint->getOptions())
            ->multiple($constraint->isMultiple())
            ->searchable($constraint->isSearchable())
            ->native($constraint->isNative())
            ->optionsLimit($constraint->getOptionsLimit())
            ->required();

        if ($getOptionLabelUsing = $constraint->getOptionLabelUsingCallback()) {
            $field->getOptionLabelUsing($getOptionLabelUsing);
        }

        if ($getOptionLabelsUsing = $constraint->getOptionLabelsUsingCallback()) {
            $field->getOptionLabelsUsing($getOptionLabelsUsing);
        }

        if ($getOptionLabelFromRecordUsing = $constraint->getOptionLabelFromRecordUsingCallback()) {
            $field->getOptionLabelFromRecordUsing($getOptionLabelFromRecordUsing);
        }

        if ($getSearchResultsUsing = $constraint->getSearchResultsUsingCallback()) {
            $field->getSearchResultsUsing($getSearchResultsUsing);
        }

        return [$field];
    }

    public function getSummary(): string
    {
        $constraint = $this->getConstraint();

        $values = Arr::wrap($this->getSettings()[$constraint->isMultiple() ? 'values' : 'value']);

        $values = Arr::join($values, glue: ', ', finalGlue: ' or ');

        return $this->isInverse() ? "{$constraint->getAttributeLabel()} is not \"{$values}\"" : "{$constraint->getAttributeLabel()} is \"{$values}\"";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $value = $this->getSettings()[$this->getConstraint()->isMultiple() ? 'values' : 'value'];

        if (is_array($value)) {
            return $query->{$this->isInverse() ? 'whereNotIn' : 'whereIn'}($qualifiedColumn, $value);
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, $value);
    }

    public function getConstraint(): ?SelectConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof SelectConstraint)) {
            throw new Exception('Is operator can only be used with select constraints.');
        }

        return $constraint;
    }
}
