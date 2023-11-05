<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class EndsWithOperator extends Operator
{
    public function getName(): string
    {
        return 'endsWith';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not end with' : 'Ends with';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('text')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} does not end with \"{$this->getSettings()['text']}\"" : "{$this->getConstraint()->getAttributeLabel()} ends with \"{$this->getSettings()['text']}\"";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        if ($query->getConnection()->getDriverName() === 'pgsql') {
            $qualifiedColumn = new Expression("lower({$qualifiedColumn}::text)");
            $text = Str::lower($text);
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'like', "%{$text}");
    }
}
