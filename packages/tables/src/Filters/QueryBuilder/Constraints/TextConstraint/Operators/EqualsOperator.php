<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class EqualsOperator extends Operator
{
    public function getName(): string
    {
        return 'equals';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.text.equals.label.inverse' :
                'filament-tables::filters/query-builder.operators.text.equals.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.text.equals.summary.inverse' :
                'filament-tables::filters/query-builder.operators.text.equals.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'text' => $this->getSettings()['text'],
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('text')
                ->label(__('filament-tables::filters/query-builder.operators.text.form.text.label'))
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $isPostgres = $databaseConnection->getDriverName() === 'pgsql';

        if ($isPostgres) {
            [$table, $column] = explode('.', $qualifiedColumn);

            if (Str::lower($table) !== $table) {
                $table = (string) str($table)->wrap('"');
            }

            if (Str::lower($column) !== $column) {
                $column = (string) str($column)->wrap('"');
            }

            $qualifiedColumn = new Expression("lower({$table}.{$column}::text)");
            $text = Str::lower($text);
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'like', $text);
    }
}
