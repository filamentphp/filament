<?php

namespace Filament\Tests\Upgrade\Fixtures\QueryBuilderIcons;

use Filament\Tables\View\TablesIconAlias as Icons;

function aliases(): array
{
    return [
        'tables::filters.query-builder.constraints.boolean' => Icons::FILTERS_QUERY_BUILDER_CONSTRAINTS_BOOLEAN,
        'tables::filters.query-builder.constraints.date' => Icons::FILTERS_QUERY_BUILDER_CONSTRAINTS_DATE,
        'tables::filters.query-builder.constraints.number' => Icons::FILTERS_QUERY_BUILDER_CONSTRAINTS_NUMBER,
        'tables::filters.query-builder.constraints.relationship' => Icons::FILTERS_QUERY_BUILDER_CONSTRAINTS_RELATIONSHIP,
        'tables::filters.query-builder.constraints.select' => Icons::FILTERS_QUERY_BUILDER_CONSTRAINTS_SELECT,
        'tables::filters.query-builder.constraints.text' => \Filament\Tables\View\TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT,
    ];
}

class CustomIcons extends Icons
{
    const FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT = 'custom::text';

    public function custom(): array
    {
        return [self::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT, static::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT];
    }
}

function manual(string $class, string $constant, string $suffix): array
{
    return [
        CustomIcons::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT,
        $class::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT,
        Icons::{$constant},
        Icons::FILTERS_REMOVE_ALL_BUTTON,
        'Filament\Tables\View\TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT',
        'tables::filters.query-builder.constraints.custom',
        'tables::filters.query-builder.constraints.' . $suffix,
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\QueryBuilderIcons;

use Filament\QueryBuilder\View\QueryBuilderIconAlias;
use Filament\Tables\View\TablesIconAlias as Icons;

function aliases(): array
{
    return [
        'query-builder::constraints.boolean' => QueryBuilderIconAlias::CONSTRAINTS_BOOLEAN,
        'query-builder::constraints.date' => QueryBuilderIconAlias::CONSTRAINTS_DATE,
        'query-builder::constraints.number' => QueryBuilderIconAlias::CONSTRAINTS_NUMBER,
        'query-builder::constraints.relationship' => QueryBuilderIconAlias::CONSTRAINTS_RELATIONSHIP,
        'query-builder::constraints.select' => QueryBuilderIconAlias::CONSTRAINTS_SELECT,
        'query-builder::constraints.text' => QueryBuilderIconAlias::CONSTRAINTS_TEXT,
    ];
}

class CustomIcons extends Icons
{
    const FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT = 'custom::text';

    public function custom(): array
    {
        return [self::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT, static::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT];
    }
}

function manual(string $class, string $constant, string $suffix): array
{
    return [
        CustomIcons::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT,
        $class::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT,
        Icons::{$constant},
        Icons::FILTERS_REMOVE_ALL_BUTTON,
        'Filament\Tables\View\TablesIconAlias::FILTERS_QUERY_BUILDER_CONSTRAINTS_TEXT',
        'tables::filters.query-builder.constraints.custom',
        'tables::filters.query-builder.constraints.' . $suffix,
    ];
}
