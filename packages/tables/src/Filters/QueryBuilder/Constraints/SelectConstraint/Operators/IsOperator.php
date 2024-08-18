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
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.select.is.label.inverse' :
                'filament-tables::filters/query-builder.operators.select.is.label.direct',
        );
    }

    public function getSummary(): string
    {
        $constraint = $this->getConstraint();
        $settings = $this->getSettings();

        if ($constraint->isMultiple()) {
            $getLabels = $constraint->getOptionLabelsUsingCallback();
            $valuesKey = 'values';
        } else {
            $getLabels = $constraint->getOptionLabelUsingCallback();
            $valuesKey = 'value';
        }

        $labels = $getLabels ?
            Arr::wrap($this->evaluate($getLabels, [$valuesKey => $settings[$valuesKey]])) :
            Arr::only($constraint->getOptions(), $settings[$valuesKey]);

        $joinedValues = Arr::join(
            $labels,
            glue: __('filament-tables::filters/query-builder.operators.select.is.summary.values_glue.0'),
            finalGlue: __('filament-tables::filters/query-builder.operators.select.is.summary.values_glue.final'),
        );

        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.select.is.summary.inverse' :
                'filament-tables::filters/query-builder.operators.select.is.summary.direct',
            [
                'attribute' => $constraint->getAttributeLabel(),
                'values' => $joinedValues,
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        $constraint = $this->getConstraint();

        $field = Select::make($constraint->isMultiple() ? 'values' : 'value')
            ->label(__($constraint->isMultiple() ? 'filament-tables::filters/query-builder.operators.select.is.form.values.label' : 'filament-tables::filters/query-builder.operators.select.is.form.value.label'))
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
