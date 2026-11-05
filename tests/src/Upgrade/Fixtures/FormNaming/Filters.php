<?php

namespace Filament\Tests\Upgrade\Fixtures\FormNaming;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

function filterForm(Table $table): Schema
{
    return Filter::make('accessor')->table($table)->getSchema();
}

function computedFilter(): Filter
{
    return Filter::make('computed')->schema(schema: [TextInput::make('computed')]);
}

function filters(): array
{
    $evaluations = 0;
    $stored = Filter::make('stored')->schema(schema: function () use (&$evaluations): array {
        $evaluations++;

        return [TextInput::make('stored')];
    });
    $holder = new class
    {
        public Filter $filter;
    };
    $holder->filter = Filter::make('property')->schema(...['schema' => [TextInput::make('property')]]);
    $components = [
        Filter::make('chain')->schema([])->label('Chain')->hasSchema(),
        array_map(static fn (TextInput $field): string => $field->getName(), $stored->getSchemaComponents()),
        array_map(static fn (TextInput $field): string => $field->getName(), computedFilter()->getSchemaComponents()),
        array_map(static fn (TextInput $field): string => $field->getName(), $holder->filter->getSchemaComponents()),
        $stored->schema(null)->hasSchema(),
        $evaluations,
    ];

    $components[] = Filter::configureUsing(
        static fn (Filter $filter) => $filter->schema([TextInput::make('global')]),
        during: static fn (): string => Filter::make('global')->getSchemaComponents()[0]->getName(),
    );

    return $components;
}
