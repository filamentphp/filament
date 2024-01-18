<?php

namespace Filament\Actions\Commands\Concerns;

use Doctrine\DBAL\Types;
use Illuminate\Support\Str;

trait CanGenerateImporterColumns
{
    protected function getImporterColumns(string $model): string
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

            if (str($columnName)->is([
                app($model)->getKeyName(),
                'created_at',
                'deleted_at',
                'updated_at',
                '*_token',
            ])) {
                continue;
            }

            $columnData = [];

            if (in_array($columnName, [
                'id',
                'sku',
                'uuid',
            ])) {
                $columnData['label'] = [Str::upper($columnName)];
            }

            if ($column->getNotnull()) {
                $columnData['rules'][0][] = 'required';
                $columnData['requiredMapping'] = [];
            }

            if (str($columnName)->contains(['email'])) {
                $columnData['rules'][0][] = 'email';
            }

            if (
                str($columnName)->endsWith('_id') &&
                filled($guessedRelationshipName = $this->guessBelongsToRelationshipName($column, $model))
            ) {
                $columnName = $guessedRelationshipName;
                $columnData['relationship'] = [];
            } elseif (in_array($column->getType()::class, [
                Types\BooleanType::class,
            ])) {
                $columnData['rules'][0][] = 'boolean';
                $columnData['boolean'] = [];
            } elseif (in_array($column->getType()::class, [
                Types\DateImmutableType::class,
                Types\DateType::class,
            ])) {
                $columnData['rules'][0][] = 'date';
            } elseif (in_array($column->getType()::class, [
                Types\DateTimeImmutableType::class,
                Types\DateTimeType::class,
                Types\DateTimeTzImmutableType::class,
                Types\DateTimeTzType::class,
            ])) {
                $columnData['rules'][0][] = 'datetime';
            } elseif (in_array($column->getType()::class, [
                Types\IntegerType::class,
                Types\SmallIntType::class,
                Types\BigIntType::class,
            ])) {
                $columnData['rules'][0][] = 'integer';
                $columnData['numeric'] = [];
            } elseif (in_array($column->getType()::class, [
                Types\DecimalType::class,
                Types\FloatType::class,
            ])) {
                $columnData['rules'][0][] = 'integer';
                $columnData['numeric'] = [];
            } elseif ($length = $column->getLength()) {
                $columnData['rules'][0][] = "max:{$length}";
            }

            // Move rules to the end of the column definition.
            if (array_key_exists('rules', $columnData)) {
                $rules = $columnData['rules'];
                unset($columnData['rules']);

                $columnData['rules'] = $rules;
            }

            $columns[$columnName] = $columnData;
        }

        $output = count($columns) ? '' : '//';

        foreach ($columns as $columnName => $columnData) {
            // Constructor
            $output .= 'ImportColumn::make(\'';
            $output .= $columnName;
            $output .= '\')';

            // Configuration
            foreach ($columnData as $methodName => $parameters) {
                $output .= PHP_EOL;
                $output .= '    ->';
                $output .= $methodName;
                $output .= '(';
                $output .= collect($parameters)
                    ->map(function (mixed $parameterValue, int | string $parameterName): string {
                        $parameterValue = match (true) {
                            /** @phpstan-ignore-next-line */
                            is_bool($parameterValue) => $parameterValue ? 'true' : 'false',
                            /** @phpstan-ignore-next-line */
                            is_null($parameterValue) => 'null',
                            is_numeric($parameterValue) => $parameterValue,
                            is_array($parameterValue) => '[\'' . implode('\', \'', $parameterValue) . '\']',
                            default => "'{$parameterValue}'",
                        };

                        if (is_numeric($parameterName)) {
                            return $parameterValue;
                        }

                        return "{$parameterName}: {$parameterValue}";
                    })
                    ->implode(', ');
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
