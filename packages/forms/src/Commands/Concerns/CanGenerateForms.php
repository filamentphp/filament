<?php

namespace Filament\Forms\Commands\Concerns;

use Filament\Forms;
use Illuminate\Support\Str;

trait CanGenerateForms
{
    protected function getResourceFormSchema(string $model): string
    {
        $model = $this->getModel($model);

        if (blank($model)) {
            return '//';
        }

        $schema = $this->getModelSchema($model);
        $table = $this->getModelTable($model);

        $components = [];

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

            $type = $this->parseColumnType($column);

            $componentData = [];

            $componentData['type'] = match (true) {
                $type['name'] === 'boolean' => Forms\Components\Toggle::class,
                $type['name'] === 'date' => Forms\Components\DatePicker::class,
                in_array($type['name'], ['datetime', 'timestamp']) => Forms\Components\DateTimePicker::class,
                $type['name'] === 'text' => Forms\Components\Textarea::class,
                $columnName === 'image', str($columnName)->startsWith('image_'), str($columnName)->contains('_image_'), str($columnName)->endsWith('_image') => Forms\Components\FileUpload::class,
                default => Forms\Components\TextInput::class,
            };

            if (str($columnName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($columnName, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($columnName, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $componentData['type'] = Forms\Components\Select::class;
                    $componentData['relationship'] = [$guessedRelationshipName, $guessedRelationshipTitleColumnName];
                }
            }

            if (in_array($columnName, [
                'id',
                'sku',
                'uuid',
            ])) {
                $componentData['label'] = [Str::upper($columnName)];
            }

            if ($componentData['type'] === Forms\Components\TextInput::class) {
                if (str($columnName)->contains(['email'])) {
                    $componentData['email'] = [];
                }

                if (str($columnName)->contains(['password'])) {
                    $componentData['password'] = [];
                }

                if (str($columnName)->contains(['phone', 'tel'])) {
                    $componentData['tel'] = [];
                }
            }

            if ($componentData['type'] === Forms\Components\FileUpload::class) {
                $componentData['image'] = [];
            }

            if (! $column['nullable']) {
                $componentData['required'] = [];
            }

            if (in_array($type['name'], [
                'integer',
                'decimal',
                'float',
                'double',
                'money',
            ])) {
                if ($componentData['type'] === Forms\Components\TextInput::class) {
                    $componentData['numeric'] = [];
                }

                if (filled($column['default'])) {
                    $componentData['default'] = [$this->parseDefaultExpression($column, $model)];
                }

                if (in_array($columnName, [
                    'cost',
                    'money',
                    'price',
                ]) || $type['name'] === 'money') {
                    $componentData['prefix'] = ['$'];
                }
            } elseif (in_array($componentData['type'], [
                Forms\Components\TextInput::class,
                Forms\Components\Textarea::class,
            ]) && isset($type['length'])) {
                $componentData['maxLength'] = [$type['length']];

                if (filled($column['default'])) {
                    $componentData['default'] = [$this->parseDefaultExpression($column, $model)];
                }
            }

            if ($componentData['type'] === Forms\Components\Textarea::class) {
                $componentData['columnSpanFull'] = [];
            }

            $components[$columnName] = $componentData;
        }

        $output = count($components) ? '' : '//';

        foreach ($components as $componentName => $componentData) {
            // Constructor
            $output .= (string) str($componentData['type'])->after('Filament\\');
            $output .= '::make(\'';
            $output .= $componentName;
            $output .= '\')';

            unset($componentData['type']);

            // Configuration
            foreach ($componentData as $methodName => $parameters) {
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

            if (! (array_key_last($components) === $componentName)) {
                $output .= PHP_EOL;
            }
        }

        return $output;
    }
}
