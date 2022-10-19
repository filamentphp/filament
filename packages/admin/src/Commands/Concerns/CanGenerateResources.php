<?php

namespace Filament\Commands\Concerns;

use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
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

            $guessedRelationName = null;
            if(Str::of($column->getName())->endsWith('_id')){

                $guessedRelationName = $this->guessBelongsToSelectRelationName($column,$model);
                if(!empty($guessedRelationName)){
                    $titleColumnName = $this->guessBelongsToSelectRelationTitleColumnName($column,$model);
                    $componentData['type'] = Forms\Components\Select::class;
                    $componentData['relationship'] = ["'$guessedRelationName",$titleColumnName."'"];
                }
            }

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

            if (in_array($type, [Forms\Components\TextInput::class, Forms\Components\Textarea::class]) && ($length = $column->getLength()) && empty($guessedRelationName)) {
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

    protected function getTableSchemaByTableName(string $model, $tableName): ?Table
    {
        if ((! class_exists($model)) && (! class_exists($model = "App\\Models\\{$model}"))) {
            return null;
        }

        $model = app($model);

        try {
            return $model
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableDetails($tableName);
        } catch (Throwable $exception) {
            return null;
        }
    }

    protected function guessBelongsToSelectRelationName(AbstractAsset $column, string $model) :string
    {
        $modelReflection = invade(new $model);
        $hasRelation = false;
        $guessedRelationName = Str::of($column->getName())->replaceLast("_id",'');
        if($modelReflection->reflected->hasMethod($guessedRelationName)){
            $hasRelation = true;
        }

        if(!$hasRelation){
            $guessedRelationName = $guessedRelationName->camel();
            if($modelReflection->reflected->hasMethod($guessedRelationName)){
                $hasRelation = true;
            }
        }


        try {
            if ($modelReflection->reflected->getMethod($guessedRelationName)->getReturnType() == BelongsTo::class) {
                $hasRelation = true;
            }
        } catch (\ReflectionException $e) {
            $hasRelation = false;
        }

        return  $hasRelation?$guessedRelationName:"";
    }

    protected function guessBelongsToSelectRelationTableName(AbstractAsset $column) : string{
        $tempTableName = Str::of($column->getName())->replaceLast("_id",'');
        $tableName = "";
        if(Schema::hasTable(Str::of($tempTableName)->plural())){
            $tableName = Str::of($tempTableName)->plural();
        }else if(Schema::hasTable($tempTableName)){
            $tableName =$tempTableName;
        }
        return $tableName;
    }

    protected function guessBelongsToSelectRelationTitleColumnName(AbstractAsset $column, string $model) : string{
        $tableName = $this->guessBelongsToSelectRelationTableName($column);
        $titleKey = "";
        $primaryKey ="";
        if(empty($tableName)){
            $titleKey = "id";
        }else{
            $schema = $this->getTableSchemaByTableName($model,$tableName);
            $primaryKey = $schema->getPrimaryKey()->getColumns()[0];
            $columns = collect(array_keys($schema->getColumns()));
            $titleKey = $columns->contains('name')
                ?"name":($columns->contains('title')?'title':"");
        }

        return empty($titleKey)?$primaryKey:$titleKey;
    }
}
