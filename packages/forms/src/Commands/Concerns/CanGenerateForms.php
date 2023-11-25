<?php

namespace Filament\Forms\Commands\Concerns;

use Doctrine\DBAL\Types;
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

        $table = $this->getModelTable($model);

        if (blank($table)) {
            return '//';
        }

        $components = [];

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

            $componentData = [];

            $componentData['type'] = match (true) {
                $column->getType()::class === Types\BooleanType::class => Forms\Components\Toggle::class,
                in_array($column->getType()::class, [Types\DateImmutableType::class, Types\DateType::class]) => Forms\Components\DatePicker::class,
                in_array($column->getType()::class, [Types\DateTimeImmutableType::class, Types\DateTimeType::class, Types\DateTimeTzImmutableType::class, Types\DateTimeTzType::class]) => Forms\Components\DateTimePicker::class,
                $column->getType()::class === Types\TextType::class => Forms\Components\Textarea::class,
                $columnName === 'image', str($columnName)->startsWith('image_'), str($columnName)->contains('_image_'), str($columnName)->endsWith('_image') => Forms\Components\FileUpload::class,
                default => Forms\Components\TextInput::class,
            };

            if (str($columnName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($column, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($column, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $componentData['type'] = Forms\Components\Select::class;
                    $componentData['relationship'] = [$guessedRelationshipName, $guessedRelationshipTitleColumnName];
                }
            }

            if (in_array($columnName, [
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

            if ($column->getNotnull()) {
                $componentData['required'] = [];
            }

            if (in_array($column->getType()::class, [
                Types\BigIntType::class,
                Types\DecimalType::class,
                Types\FloatType::class,
                Types\IntegerType::class,
                Types\SmallIntType::class,
            ])) {
                if ($componentData['type'] === Forms\Components\TextInput::class) {
                    $componentData['numeric'] = [];
                }

                if (filled($column->getDefault())) {
                    $componentData['default'] = [$column->getDefault()];
                }

                if (in_array($columnName, [
                    'cost',
                    'money',
                    'price',
                ])) {
                    $componentData['prefix'] = ['$'];
                }
            } elseif (in_array($componentData['type'], [
                Forms\Components\TextInput::class,
                Forms\Components\Textarea::class,
            ]) && ($length = $column->getLength())) {
                $componentData['maxLength'] = [$length];

                if (filled($column->getDefault())) {
                    $componentData['default'] = [$column->getDefault()];
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
