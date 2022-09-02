<?php

namespace Filament\Commands\Concerns;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Throwable;

trait CanGenerateResources
{
    protected function getResourceFormSchema(string $model): string
    {
        $table = $this->getModelTable($model);

        if (! $table) {
            return $this->indentString('//', 4);
        }

        $components = [];

        foreach ($table->getColumns() as $column) {
            if ($column->getAutoincrement()) {
                continue;
            }

            if (Str::of($column->getName())->is([
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

            if ($type === Forms\Components\TextInput::class) {
                if (Str::of($column->getName())->contains(['email'])) {
                    $componentData['email'] = [];
                }

                if (Str::of($column->getName())->contains(['password'])) {
                    $componentData['password'] = [];
                }

                if (Str::of($column->getName())->contains(['phone', 'tel'])) {
                    $componentData['tel'] = [];
                }
            }

            if ($column->getNotnull()) {
                $componentData['required'] = [];
            }

            if (in_array($type, [Forms\Components\TextInput::class, Forms\Components\Textarea::class]) && ($length = $column->getLength())) {
                $componentData['maxLength'] = [$length];
            }

            $components[$column->getName()] = $componentData;
        }

        $output = count($components) ? '' : '//';

        foreach ($components as $componentName => $componentData) {
            // Constructor
            $output .= (string) Str::of($componentData['type'])->after('Filament\\');
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

        return $this->indentString($output, 4);
    }

    protected function getResourceTableColumns(string $model): string
    {
        $table = $this->getModelTable($model);

        if (! $table) {
            return $this->indentString('//', 4);
        }

        $columns = [];

        foreach ($table->getColumns() as $column) {
            if ($column->getAutoincrement()) {
                continue;
            }

            if (Str::of($column->getName())->endsWith([
                '_token',
            ])) {
                continue;
            }

            if (Str::of($column->getName())->contains([
                'password',
            ])) {
                continue;
            }

            $columnData = [];

            $columnData['type'] = $type = match ($column->getType()::class) {
                Types\BooleanType::class => Tables\Columns\BooleanColumn::class,
                default => Tables\Columns\TextColumn::class,
            };

            if ($type === Tables\Columns\TextColumn::class) {
                if ($column->getType()::class === Types\DateType::class) {
                    $columnData['date'] = [];
                }

                if ($column->getType()::class === Types\DateTimeType::class) {
                    $columnData['dateTime'] = [];
                }
            }

            $columns[$column->getName()] = $columnData;
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

        return $this->indentString($output, 4);
    }

    protected function getModelTable(string $model): ?Table
    {
        if ((! class_exists($model)) && (! class_exists($model = "App\\Models\\{$model}"))) {
            return null;
        }

        $model = app($model);

        try {
            return $model
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableDetails($model->getTable());
        } catch (Throwable $exception) {
            return null;
        }
    }
}
