<?php

namespace Filament\Tables\Commands\FileGenerators\Concerns;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nette\PhpGenerator\Literal;

trait CanGenerateModelTables
{
    /**
     * @param  ?class-string<Model>  $model
     */
    public function generateTableMethodBody(?string $model = null): string
    {
        $this->importUnlessPartial(BulkActionGroup::class);

        return <<<PHP
            return \$table
                ->query(fn (): {$this->simplifyFqn(Builder::class)} => {$this->simplifyFqn($model)}::query())
                ->columns([
                    {$this->outputTableColumns($model)}
                ])
                ->filters([
                    //
                ])
                ->headerActions([
                    //
                ])
                ->recordActions([
                    //
                ])
                ->toolbarActions([
                    {$this->simplifyFqn(BulkActionGroup::class)}::make([
                        //
                    ]),
                ]);
            PHP;
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     * @return array<string>
     */
    public function getTableColumns(?string $model = null, array $exceptColumns = []): array
    {
        if (! $this->isGenerated()) {
            return [];
        }

        if (blank($model)) {
            return [];
        }

        if (! class_exists($model)) {
            return [];
        }

        $schema = $this->getModelSchema($model);
        $table = $this->getModelTable($model);

        $columns = [];

        foreach ($schema->getColumns($table) as $column) {
            if ($column['auto_increment']) {
                continue;
            }

            $type = $this->parseColumnType($column);

            if (in_array($type['name'], [
                'json',
                'text',
            ])) {
                continue;
            }

            $columnName = $column['name'];

            if (in_array($columnName, $exceptColumns)) {
                continue;
            }

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
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($columnName, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($columnName, app($model)->{$guessedRelationshipName}()->getModel()::class);

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

            if ($columnName === 'email') {
                $columnData['label'] = ['Email address'];
            }

            if ($type['name'] === 'boolean') {
                $columnData['type'] = IconColumn::class;
                $columnData['boolean'] = [];
            } else {
                $columnData['type'] = match (true) {
                    $columnName === 'image', str($columnName)->startsWith('image_'), str($columnName)->contains('_image_'), str($columnName)->endsWith('_image') => ImageColumn::class,
                    default => TextColumn::class,
                };

                if (in_array($type['name'], [
                    'string',
                    'char',
                ]) && ($columnData['type'] === TextColumn::class)) {
                    $columnData['searchable'] = [];
                }

                if ($type['name'] === 'date') {
                    $columnData['date'] = [];
                    $columnData['sortable'] = [];
                }

                if ($type['name'] === 'time') {
                    $columnData['time'] = [];
                    $columnData['sortable'] = [];
                }

                if (in_array($type['name'], [
                    'datetime',
                    'timestamp',
                ])) {
                    $columnData['dateTime'] = [];
                    $columnData['sortable'] = [];
                }

                if (in_array($type['name'], [
                    'integer',
                    'decimal',
                    'float',
                    'double',
                    'money',
                ])) {
                    $columnData[in_array($columnName, [
                        'cost',
                        'money',
                        'price',
                    ]) || $type['name'] === 'money' ? 'money' : 'numeric'] = [];
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

            $this->importUnlessPartial($columnData['type']);

            $columns[$columnName] = $columnData;
        }

        return array_map(
            function (array $columnData, string $columnName): string {
                $column = (string) new Literal("{$this->simplifyFqn($columnData['type'])}::make(?)", [$columnName]);

                unset($columnData['type']);

                foreach ($columnData as $methodName => $parameters) {
                    $column .= new Literal(PHP_EOL . "            ->{$methodName}(...?:)", [$parameters]);
                }

                return "{$column},";
            },
            $columns,
            array_keys($columns),
        );
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function outputTableColumns(?string $model = null, array $exceptColumns = []): string
    {
        $columns = $this->getTableColumns($model, $exceptColumns);

        if (empty($columns)) {
            return '//';
        }

        return implode(PHP_EOL . '        ', $columns);
    }
}
