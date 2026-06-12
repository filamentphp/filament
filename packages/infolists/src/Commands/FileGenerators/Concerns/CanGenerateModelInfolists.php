<?php

namespace Filament\Infolists\Commands\FileGenerators\Concerns;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nette\PhpGenerator\Literal;

trait CanGenerateModelInfolists
{
    /**
     * @param  ?class-string<Model>  $model
     */
    public function generateInfolistMethodBody(?string $model = null): string
    {
        return <<<PHP
            return \$schema
                ->columns([
                    {$this->outputInfolistComponents($model)}
                ]);
            PHP;
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     * @return array<string>
     */
    public function getInfolistComponents(?string $model = null, array $exceptColumns = []): array
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

        $components = [];

        foreach ($schema->getColumns($table) as $column) {
            if ($column['auto_increment']) {
                continue;
            }

            $type = $this->parseColumnType($column);

            if (in_array($type['name'], [
                'json',
            ])) {
                continue;
            }

            $componentName = $column['name'];

            if (in_array($componentName, $exceptColumns)) {
                continue;
            }

            if (str($componentName)->endsWith([
                '_token',
            ])) {
                continue;
            }

            if (str($componentName)->contains([
                'password',
            ])) {
                continue;
            }

            $componentData = [];

            if (str($componentName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($componentName, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($componentName, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $componentName = "{$guessedRelationshipName}.{$guessedRelationshipTitleColumnName}";

                    $componentData['label'] = [(string) str($guessedRelationshipName)
                        ->kebab()
                        ->replace(['-', '_'], ' ')
                        ->ucfirst()];
                }
            } else {
                $guessedRelationshipName = null;
            }

            if (in_array($componentName, [
                'id',
                'sku',
                'uuid',
            ])) {
                $componentData['label'] = [Str::upper($componentName)];
            }

            if ($componentName === 'email') {
                $componentData['label'] = ['Email address'];
            }

            if ($type['name'] === 'boolean') {
                $componentData['type'] = IconEntry::class;
                $componentData['boolean'] = [];
            } else {
                $componentData['type'] = match (true) {
                    $componentName === 'image', str($componentName)->startsWith('image_'), str($componentName)->contains('_image_'), str($componentName)->endsWith('_image') => ImageEntry::class,
                    default => TextEntry::class,
                };

                if (($type['name'] === 'enum') || array_key_exists($componentName, $this->getEnumCasts($model))) {
                    $componentData['badge'] = [];
                }

                if ($type['name'] === 'date') {
                    $componentData['date'] = [];
                }

                if ($type['name'] === 'time') {
                    $componentData['time'] = [];
                }

                if (in_array($type['name'], [
                    'datetime',
                    'timestamp',
                ])) {
                    $componentData['dateTime'] = [];
                }

                if (in_array($type['name'], [
                    'integer',
                    'decimal',
                    'float',
                    'double',
                    'money',
                ]) && blank($guessedRelationshipName)) {
                    $componentData[(in_array($componentName, [
                        'cost',
                        'money',
                        'price',
                    ]) || str($componentName)->endsWith([
                        '_cost',
                        '_price',
                    ]) || $type['name'] === 'money') ? 'money' : 'numeric'] = [];
                }
            }

            if (in_array($componentName, [
                'deleted_at',
            ])) {
                $componentData['visible'] = [new Literal('fn (' . class_basename($model) . ' $record): bool => $record->trashed()')];
                $this->namespace->addUse($model);
            } elseif ($column['nullable']) {
                $componentData['placeholder'] = ['-'];
            }

            if (in_array($type['name'], [
                'text',
            ])) {
                $componentData['columnSpanFull'] = [];
            }

            $this->importUnlessPartial($componentData['type']);

            $components[$componentName] = $componentData;
        }

        return array_map(
            function (array $componentData, string $componentName): string {
                $component = (string) new Literal("{$this->simplifyFqn($componentData['type'])}::make(?)", [$componentName]);

                unset($componentData['type']);

                foreach ($componentData as $methodName => $parameters) {
                    $component .= new Literal(PHP_EOL . "            ->{$methodName}(...?:)", [$parameters]);
                }

                return "{$component},";
            },
            $components,
            array_keys($components),
        );
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function outputInfolistComponents(?string $model = null, array $exceptColumns = []): string
    {
        $columns = $this->getInfolistComponents($model, $exceptColumns);

        if (empty($columns)) {
            return '//';
        }

        return implode(PHP_EOL . '        ', $columns);
    }
}
