<?php

namespace Filament\Forms\Commands\Concerns;

use Doctrine\DBAL\Types;
use Filament\Forms;

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
                'created_at',
                'deleted_at',
                'updated_at',
                '*_token',
            ])) {
                continue;
            }

            $componentData = [];

            $componentData['type'] = $type = match ($column->getType()::class) {
                Types\BooleanType::class => Forms\Components\Toggle::class,
                Types\DateType::class => Forms\Components\DatePicker::class,
                Types\DateTimeType::class => Forms\Components\DateTimePicker::class,
                Types\TextType::class => Forms\Components\Textarea::class,
                default => Forms\Components\TextInput::class,
            };

            if (str($columnName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($column, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($column, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $componentData['type'] = $type = Forms\Components\Select::class;
                    $componentData['relationship'] = ["'{$guessedRelationshipName}", "{$guessedRelationshipTitleColumnName}'"];
                }
            }

            if ($type === Forms\Components\TextInput::class) {
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

            if ($column->getNotnull()) {
                $componentData['required'] = [];
            }

            if (in_array($type, [Forms\Components\TextInput::class, Forms\Components\Textarea::class]) && ($length = $column->getLength())) {
                $componentData['maxLength'] = [$length];
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
                $output .= implode('\', \'', $parameters);
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
