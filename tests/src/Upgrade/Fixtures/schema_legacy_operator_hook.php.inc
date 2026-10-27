<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaLegacyOperatorHook;

use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;

class CustomOperator extends ContainsOperator
{
    public function getFormSchema(): array
    {
        return [...parent::getFormSchema(), 'custom'];
    }
}

function inspect(CustomOperator $operator, ContainsOperator $builtIn): void
{
    $operator->getFormSchema();
    $builtIn->getFormSchema();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaLegacyOperatorHook;

use Filament\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;

class CustomOperator extends ContainsOperator
{
    public function getSchemaComponents(): array
    {
        return [...parent::getSchemaComponents(), 'custom'];
    }
}

function inspect(CustomOperator $operator, ContainsOperator $builtIn): void
{
    $operator->getSchemaComponents();
    $builtIn->getSchemaComponents();
}
