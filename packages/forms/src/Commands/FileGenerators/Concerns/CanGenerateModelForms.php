<?php

namespace Filament\Forms\Commands\FileGenerators\Concerns;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nette\PhpGenerator\Literal;

trait CanGenerateModelForms
{
    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function generateFormMethodBody(?string $model = null, ?string $statePath = null, ?string $modelMethodOutput = null, array $exceptColumns = []): string
    {
        $statePathOutput = filled($statePath)
            ? PHP_EOL . new Literal('    ->statePath(?)', [$statePath])
            : '';

        $modelMethodOutput = filled($modelMethodOutput)
            ? PHP_EOL . '    ' . $modelMethodOutput
            : '';

        return <<<PHP
            return \$schema
                ->components([
                    {$this->outputFormComponents($model, $exceptColumns)}
                ]){$statePathOutput}{$modelMethodOutput};
            PHP;
    }

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     * @return array<string>
     */
    public function getFormComponents(?string $model = null, array $exceptColumns = []): array
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

            $componentName = $column['name'];

            if (in_array($componentName, $exceptColumns)) {
                continue;
            }

            if (str($componentName)->is([
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
                $type['name'] === 'boolean' => Toggle::class,
                $type['name'] === 'date' => DatePicker::class,
                $type['name'] === 'time' => TimePicker::class,
                in_array($type['name'], ['datetime', 'timestamp']) => DateTimePicker::class,
                $type['name'] === 'text' => Textarea::class,
                $componentName === 'image', str($componentName)->startsWith('image_'), str($componentName)->contains('_image_'), str($componentName)->endsWith('_image') => FileUpload::class,
                default => TextInput::class,
            };

            $enumCasts = $this->getEnumCasts($model);

            if (isset($type['name']) && (($type['name'] === 'enum') || array_key_exists($componentName, $enumCasts))) {
                $componentData['type'] = Select::class;

                if (array_key_exists($componentName, $enumCasts)) {
                    $enumClass = $enumCasts[$componentName];

                    $this->namespace->addUse($enumClass);

                    $componentData['options'] = [new Literal(class_basename($enumClass) . '::class')];
                } else {
                    $componentData['options'] = [array_combine(
                        $type['values'],
                        array_map(
                            fn (string $value): string => (string) str($value)
                                ->kebab()
                                ->replace(['-', '_'], ' ')
                                ->ucfirst(),
                            $type['values'],
                        ),
                    )];
                }

                if ($column['default']) {
                    $componentData['default'] = [$this->parseDefaultExpression($column, $model)];
                }
            }

            if (str($componentName)->endsWith('_id')) {
                $guessedRelationshipName = $this->guessBelongsToRelationshipName($componentName, $model);

                if (filled($guessedRelationshipName)) {
                    $guessedRelationshipTitleColumnName = $this->guessBelongsToRelationshipTitleColumnName($componentName, app($model)->{$guessedRelationshipName}()->getModel()::class);

                    $componentData['type'] = Select::class;
                    $componentData['relationship'] = [$guessedRelationshipName, $guessedRelationshipTitleColumnName];
                }
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

            if ($componentData['type'] === TextInput::class) {
                if (str($componentName)->contains(['email'])) {
                    $componentData['email'] = [];
                }

                if (str($componentName)->contains(['password'])) {
                    $componentData['password'] = [];
                }

                if (str($componentName)->contains(['phone', 'tel'])) {
                    $componentData['tel'] = [];
                }

                if (in_array($componentName, ['url', 'website']) || str($componentName)->endsWith(['_url', '_website'])) {
                    $componentData['url'] = [];
                }
            }

            if ($componentData['type'] === FileUpload::class) {
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
                if ($componentData['type'] === TextInput::class) {
                    $componentData['numeric'] = [];
                }

                if (filled($column['default'])) {
                    $componentData['default'] = [$this->parseDefaultExpression($column, $model)];

                    if (is_numeric($componentData['default'][0])) {
                        $componentData['default'] = [$componentData['default'][0] + 0];
                    }
                }

                if (in_array($componentName, [
                    'cost',
                    'money',
                    'price',
                ]) || str($componentName)->endsWith([
                    '_cost',
                    '_price',
                ]) || $type['name'] === 'money') {
                    $componentData['prefix'] = ['$'];
                }
            } elseif (in_array($componentData['type'], [
                TextInput::class,
                Textarea::class,
            ])) {
                if (isset($column['length'])) { /** @phpstan-ignore isset.offset */
                    $componentData['maxLength'] = [$column['length']];
                }

                if (filled($column['default'])) {
                    $componentData['default'] = [$this->parseDefaultExpression($column, $model)];
                }
            }

            if ($componentData['type'] === Textarea::class) {
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
    public function outputFormComponents(?string $model = null, array $exceptColumns = []): string
    {
        $components = $this->getFormComponents($model, $exceptColumns);

        if (empty($components)) {
            return '//';
        }

        return implode(PHP_EOL . '        ', $components);
    }

    public function isGenerated(): bool
    {
        return true;
    }
}
