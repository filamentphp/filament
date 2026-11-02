<?php

namespace Filament\QueryBuilder\Constraints\SelectConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use LogicException;

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
                'filament-query-builder::query-builder.operators.select.is.label.inverse' :
                'filament-query-builder::query-builder.operators.select.is.label.direct',
        );
    }

    public function getSummary(): string
    {
        $constraint = $this->getConstraint();

        if ($constraint->isMultiple()) {
            $getLabels = $constraint->getOptionLabelsUsingCallback();
            $valuesKey = 'values';
        } else {
            $getLabels = $constraint->getOptionLabelUsingCallback();
            $valuesKey = 'value';
        }

        $values = $this->getValueSetting();

        $labels = $getLabels ?
            Arr::wrap($this->evaluate($getLabels, [$valuesKey => $values])) :
            Arr::only($constraint->getOptions(), Arr::wrap($values));

        $joinedValues = Arr::join(
            $labels,
            glue: __('filament-query-builder::query-builder.operators.select.is.summary.values_glue.0'),
            finalGlue: __('filament-query-builder::query-builder.operators.select.is.summary.values_glue.final'),
        );

        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.select.is.summary.inverse' :
                'filament-query-builder::query-builder.operators.select.is.summary.direct',
            [
                'attribute' => $constraint->getAttributeLabel(),
                'values' => $joinedValues,
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        $constraint = $this->getConstraint();

        $field = Select::make($constraint->isMultiple() ? 'values' : 'value')
            ->label(__($constraint->isMultiple() ? 'filament-query-builder::query-builder.operators.select.is.form.values.label' : 'filament-query-builder::query-builder.operators.select.is.form.value.label'))
            ->options($constraint->getOptions())
            ->multiple($constraint->isMultiple())
            ->searchable($constraint->isSearchable())
            ->native($constraint->isNative())
            ->optionsLimit($constraint->getOptionsLimit())
            ->required()
            ->columnSpanFull();

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
        $value = $this->getValueSetting();

        if (is_array($value)) {
            // Security: nothing valid remains to filter by after discarding tampered values.
            if ($value === []) {
                return $query;
            }

            return $query->{$this->isInverse() ? 'whereNotIn' : 'whereIn'}($qualifiedColumn, $value);
        }

        // Security: skip applying the constraint when the tampered single value is not a scalar.
        if ($value === null) {
            return $query;
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, $value);
    }

    protected function getValueSetting(): mixed
    {
        $isMultiple = $this->getConstraint()->isMultiple();

        $value = $this->getSettings()[$isMultiple ? 'values' : 'value'] ?? null;

        // Security: settings arrive from the request payload and can be tampered with. A single
        // select value must be a scalar, and a multiple select value must be a list of scalars;
        // any other shape (e.g. a nested array) would reach `strval()` / `Arr::only()` and throw.
        // Fail closed by discarding non-scalar values.
        if ($isMultiple) {
            return is_array($value)
                ? array_values(array_filter($value, is_scalar(...)))
                : [];
        }

        return is_scalar($value) ? $value : null;
    }

    public function getConstraint(): ?SelectConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof SelectConstraint)) {
            throw new LogicException('Is operator can only be used with select constraints.');
        }

        return $constraint;
    }
}
