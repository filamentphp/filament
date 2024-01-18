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

            if (in_array($column->getType()::class, [
                Types\JsonType::class,
                Types\TextType::class,
            ])) {
                continue;
            }

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

            if ($column->getType() instanceof Types\BooleanType) {
                $columnData['type'] = Tables\Columns\IconColumn::class;
                $columnData['boolean'] = [];
            } else {
                $columnData['type'] = match (true) {
                    $columnName === 'image', str($columnName)->startsWith('image_'), str($columnName)->contains('_image_'), str($columnName)->endsWith('_image') => Tables\Columns\ImageColumn::class,
                    default => Tables\Columns\TextColumn::class,
                };

                if (in_array($column->getType()::class, [
                    Types\StringType::class,
                ]) && ($columnData['type'] === Tables\Columns\TextColumn::class)) {
                    $columnData['searchable'] = [];
                }

                if (in_array($column->getType()::class, [
                    Types\DateImmutableType::class,
                    Types\DateType::class,
                ])) {
                    $columnData['date'] = [];
                    $columnData['sortable'] = [];
                }

                if (in_array($column->getType()::class, [
                    Types\DateTimeImmutableType::class,
                    Types\DateTimeType::class,
                    Types\DateTimeTzImmutableType::class,
                    Types\DateTimeTzType::class,
                ])) {
                    $columnData['dateTime'] = [];
                    $columnData['sortable'] = [];
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
                    $columnData['sortable'] = [];
                }
            }

            if (in_array($columnName, [
                'created_at',
                'updated_at',
                'deleted_at',
            ])) {
                $columnData['toggleable'] = ['isToggledHiddenByDefault' => true];
            }

            $columns[$columnName] = $columnData;
        }

        $output = count($columns) ? '' : '//';

        foreach ($columns as $columnName => $columnData) {
            // Constructor
            $output .= (string) str($columnData['type'])->after('Filament\\');
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
                $output .= collect($parameters)
                    ->map(function (mixed $parameterValue, int | string $parameterName): string {
                        $parameterValue = match (true) {
                            /** @phpstan-ignore-next-line */
                            is_bool($parameterValue) => $parameterValue ? 'true' : 'false',
                            /** @phpstan-ignore-next-line */
                            is_null($parameterValue) => 'null',
                            is_numeric($parameterValue) => $parameterValue,
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
