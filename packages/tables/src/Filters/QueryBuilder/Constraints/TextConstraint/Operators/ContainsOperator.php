<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators;

use Filament\Forms\Components\Component;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class ContainsOperator extends Operator
{
    public function getName(): string
    {
        return 'contains';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Does not contain' : 'Contains';
    }

    /**
     * @return array<Component>
     */
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
        return $this->isInverse() ? "{$this->getConstraint()->getAttributeLabel()} does not contain \"{$this->getSettings()['text']}\"" : "{$this->getConstraint()->getAttributeLabel()} contains \"{$this->getSettings()['text']}\"";
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        if ($databaseConnection->getDriverName() === 'pgsql') {
            $qualifiedColumn = new Expression("lower({$qualifiedColumn}::text)");
            $text = Str::lower($text);
        }

        return $query->{$this->isInverse() ? 'whereNot' : 'where'}($qualifiedColumn, 'like', "%{$text}%");
    }
}
