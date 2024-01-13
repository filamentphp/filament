<?php

namespace Filament\Actions\Commands\Concerns;

use Doctrine\DBAL\Types;
use Illuminate\Support\Str;

trait CanGenerateExporterColumns
{
    protected function getExporterColumns(string $model): string
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
            $columnName = $column->getName();

            if (str($columnName)->endsWith([
                '_token',
            ])) {
                continue;
            }

            if (str($columnName)->contains([
                'password',
            ])) {
                continue;
            }

            if (str($columnName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($column, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($column, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $columnName = "{$guessedRelationshipName}.{$guessedRelationshipTitleColumnName}";
                }
            }

            $columnData = [];

            if (in_array($columnName, [
                'id',
                'sku',
                'uuid',
            ])) {
                $columnData['label'] = [Str::upper($columnName)];
            }

            if (in_array($column->getType()::class, [
                Types\DateImmutableType::class,
                Types\DateType::class,
            ])) {
                $columnData['date'] = [];
            }

            if (in_array($column->getType()::class, [
                Types\DateTimeImmutableType::class,
                Types\DateTimeType::class,
                Types\DateTimeTzImmutableType::class,
                Types\DateTimeTzType::class,
            ])) {
                $columnData['dateTime'] = [];
            }

            if (in_array($column->getType()::class, [
                Types\BigIntType::class,
                Types\DecimalType::class,
                Types\FloatType::class,
                Types\IntegerType::class,
                Types\SmallIntType::class,
            ])) {
                $columnData[in_array($columnName, [
                    'cost',
                    'money',
                    'price',
                ]) ? 'money' : 'numeric'] = [];
            }

            $columns[$columnName] = $columnData;
        }

        $output = count($columns) ? '' : '//';

        foreach ($columns as $columnName => $columnData) {
            // Constructor
            $output .= 'ExportColumn::make(\'';
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
