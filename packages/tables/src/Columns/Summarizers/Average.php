<?php

namespace Filament\Tables\Columns\Summarizers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;
use Illuminate\Support\Str;
use LogicException;

class Average extends Summarizer
{
    protected ?string $selectAlias = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    public function summarize(Builder $query, string $attribute): int | float | null
    {
        if (! ($query->getGrammar() instanceof SqlServerGrammar)) {
            return $query->avg($attribute);
        }

        $query = clone $query;

        $query->beforeQuery(function (Builder $query): void {
            if ($query->distinct && ($query->unions || $query->havings)) {
                throw new LogicException('SQL Server average summaries do not support combining distinct() with union(), unionAll(), or having() on the summary query.');
            }

            $column = $query->getGrammar()->columnize(is_array($query->distinct) ? $query->distinct : $query->aggregate['columns']);
            $arguments = $this->getSqlServerAverageArguments($column, (bool) $query->distinct);

            $query->aggregate = [
                'function' => 'coalesce',
                'columns' => array_map($query->raw(...), $arguments),
            ];
            $query->distinct = false;
        });

        return $query->avg($attribute);
    }

    /**
     * @return array<string, string>
     */
    public function getSelectStatements(string $column): array
    {
        $column = $this->getQuery()->getGrammar()->wrap($column);

        if ($this->getQuery()->getGrammar() instanceof SqlServerGrammar) {
            $arguments = implode(', ', $this->getSqlServerAverageArguments($column));

            return [
                $this->getSelectAlias() => "coalesce({$arguments})",
            ];
        }

        return [
            $this->getSelectAlias() => "avg({$column})",
        ];
    }

    /**
     * @return array<string>
     */
    protected function getSqlServerAverageArguments(string $column, bool $isDistinct = false): array
    {
        $distinct = $isDistinct ? 'distinct ' : '';

        return [
            "avg({$distinct}case when sql_variant_property({$column}, 'BaseType') not in ('tinyint', 'smallint', 'int', 'bigint') then {$column} end)",
            "avg({$distinct}cast({$column} as float))",
        ];
    }

    public function getSelectedState(): int | float | null
    {
        if (! array_key_exists($this->selectAlias ?? '', $this->selectedState)) {
            return null;
        }

        return $this->selectedState[$this->getSelectAlias()];
    }

    public function selectAlias(?string $alias): static
    {
        $this->selectAlias = $alias;

        return $this;
    }

    public function getSelectAlias(): string
    {
        return $this->selectAlias ??= Str::random();
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.average.label');
    }
}
