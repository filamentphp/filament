<?php

namespace Filament\Tables\Commands\Concerns;

use Doctrine\DBAL\Types;
use Filament\Tables;
use Illuminate\Support\Str;

trait CanGenerateTables
{
    protected function getResourceTableColumns(string $model): string
    {
        $model = $this->getModel($model);

        if (blank($model)) {
            return '//';
        }

        $table = $this->getModelTable($model);

        if (blank($table)) {
            return '//';
        }

        $columns = [];

        foreach ($table->getColumns() as $column) {
            if ($column->getAutoincrement()) {
                continue;
            }

            $columnName = $column->getName();

            if (Str::of($columnName)->endsWith([
                '_token',
            ])) {
                continue;
            }

            if (Str::of($columnName)->contains([
                'password',
            ])) {
                continue;
            }

            $columnData = [];

            if ($column->getType() instanceof Types\BooleanType) {
                $columnData['type'] = Tables\Columns\IconColumn::class;
                $columnData['boolean'] = [];
            } else {
                $columnData['type'] = Tables\Columns\TextColumn::class;

                if ($column->getType()::class === Types\DateType::class) {
                    $columnData['date'] = [];
                }

                if ($column->getType()::class === Types\DateTimeType::class) {
                    $columnData['dateTime'] = [];
                }
            }

            if (Str::of($columnName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($column, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($column, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $columnName = "{$guessedRelationshipName}.{$guessedRelationshipTitleColumnName}";
                }
            }

            $columns[$columnName] = $columnData;
        }

        $output = count($columns) ? '' : '//';

        foreach ($columns as $columnName => $columnData) {
            // Constructor
            $output .= (string) Str::of($columnData['type'])->after('Filament\\');
            $output .= '::make(\'';
            $output .= $columnName;
            $output .= '\')';

            unset($columnData['type']);

            // Configuration
            foreach ($columnData as $methodName => $parameters) {
                $output .= PHP_EOL;
                $output .= '    ->';
                $output .= $methodName;
                $output .= '(';
                $output .= implode('\', \'', $parameters);
                $output .= ')';
            }

            // Termination
            $output .= ',';

            if (! (array_key_last($columns) === $columnName)) {
                $output .= PHP_EOL;
            }
        }

        return $output;
    }
}
