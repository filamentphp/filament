<?php

namespace Filament\Actions\Commands\Concerns;

use Illuminate\Support\Str;

trait CanGenerateImporterColumns
{
    protected function getImporterColumns(string $model): string
    {
        $model = $this->getModel($model);

        if (blank($model)) {
            return '//';
        }

        $schema = $this->getModelSchema($model);
        $table = $this->getModelTable($model);

        $columns = [];

        foreach ($schema->getColumns($table) as $column) {
            if ($column['auto_increment']) {
                continue;
            }

            $columnName = $column['name'];

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

            if (! $column['nullable']) {
                $columnData['rules'][0][] = 'required';
                $columnData['requiredMapping'] = [];
            }

            if (str($columnName)->contains(['email'])) {
                $columnData['rules'][0][] = 'email';
            }

            $type = $this->parseColumnType($column);

            if (
                str($columnName)->endsWith('_id') &&
                filled($guessedRelationshipName = $this->guessBelongsToRelationshipName($columnName, $model))
            ) {
                $columnName = $guessedRelationshipName;
                $columnData['relationship'] = [];
            } elseif (in_array($type['name'], [
                'boolean',
            ])) {
                $columnData['rules'][0][] = 'boolean';
                $columnData['boolean'] = [];
            } elseif (in_array($type['name'], [
                'date',
            ])) {
                $columnData['rules'][0][] = 'date';
            } elseif (in_array($type['name'], [
                'datetime',
                'timestamp',
            ])) {
                $columnData['rules'][0][] = 'datetime';
            } elseif (in_array($type['name'], [
                'integer',
                'decimal',
                'float',
                'double',
                'money',
            ])) {
                $columnData['rules'][0][] = 'integer';
                $columnData['numeric'] = [];
            } elseif (isset($type['length'])) {
                $columnData['rules'][0][] = "max:{$type['length']}";
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
