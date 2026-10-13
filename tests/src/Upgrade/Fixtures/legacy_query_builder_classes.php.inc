<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyQueryBuilderClasses;

use Filament\QueryBuilder\Constraints\TextConstraint as CurrentTextConstraint;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Concerns\CanBeNullable;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns\CanAggregateRelationships;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint as LegacyTextConstraint;

class CustomConstraint extends Constraint
{
    use CanBeNullable;

    /** @return array<LegacyTextConstraint> */
    public function constraints(LegacyTextConstraint $constraint): array
    {
        return [$constraint, LegacyTextConstraint::make('title')];
    }
}

class CustomOperator extends Operator
{
    use CanAggregateRelationships;
}

return [
    QueryBuilder::make(),
    CurrentTextConstraint::make('content'),
    \Filament\Tables\Filters\QueryBuilder\Constraints\Constraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\Concerns\CanBeNullable::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns\CanAggregateRelationships::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\EqualsOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMaxOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\IsMinOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\Operators\IsFilledOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator::class,
    \Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator::class,
    'Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint',
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyQueryBuilderClasses;

use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\Concerns\CanBeNullable;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns\CanAggregateRelationships;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsAfterOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsBeforeOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsDateOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsMonthOperator;
use Filament\QueryBuilder\Constraints\DateConstraint\Operators\IsYearOperator;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint\Operators\EqualsOperator;
use Filament\QueryBuilder\Constraints\NumberConstraint\Operators\IsMaxOperator;
use Filament\QueryBuilder\Constraints\NumberConstraint\Operators\IsMinOperator;
use Filament\QueryBuilder\Constraints\Operators\IsFilledOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\EndsWithOperator;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;
use Filament\QueryBuilder\Constraints\TextConstraint as CurrentTextConstraint;
use Filament\Tables\Filters\QueryBuilder;

class CustomConstraint extends Constraint
{
    use CanBeNullable;

    /** @return array<\Filament\QueryBuilder\Constraints\TextConstraint> */
    public function constraints(CurrentTextConstraint $constraint): array
    {
        return [$constraint, CurrentTextConstraint::make('title')];
    }
}

class CustomOperator extends Operator
{
    use CanAggregateRelationships;
}

return [
    QueryBuilder::make(),
    CurrentTextConstraint::make('content'),
    Constraint::class,
    BooleanConstraint::class,
    IsTrueOperator::class,
    CanBeNullable::class,
    DateConstraint::class,
    IsAfterOperator::class,
    IsBeforeOperator::class,
    IsDateOperator::class,
    IsMonthOperator::class,
    IsYearOperator::class,
    NumberConstraint::class,
    CanAggregateRelationships::class,
    EqualsOperator::class,
    IsMaxOperator::class,
    IsMinOperator::class,
    IsFilledOperator::class,
    Operator::class,
    RelationshipConstraint::class,
    \Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperator::class,
    HasMaxOperator::class,
    HasMinOperator::class,
    IsEmptyOperator::class,
    IsRelatedToOperator::class,
    SelectConstraint::class,
    IsOperator::class,
    CurrentTextConstraint::class,
    ContainsOperator::class,
    EndsWithOperator::class,
    \Filament\QueryBuilder\Constraints\TextConstraint\Operators\EqualsOperator::class,
    StartsWithOperator::class,
    'Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint',
];
