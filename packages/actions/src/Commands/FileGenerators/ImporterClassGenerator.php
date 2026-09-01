<?php

namespace Filament\Actions\Commands\FileGenerators;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;
use Filament\Support\Commands\FileGenerators\ClassGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

use function Filament\Support\get_model_label;

class ImporterClassGenerator extends ClassGenerator
{
    use CanReadModelSchemas;

    /**
     * @param  class-string<Model>  $modelFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
        protected string $resolutionMode,
        protected ?string $resolutionColumn,
        protected bool $isGenerated,
    ) {}

    public function getNamespace(): string
    {
        return $this->extractNamespace($this->getFqn());
    }

    /**
     * @return array<string>
     */
    public function getImports(): array
    {
        return [
            $this->getExtends(),
            Import::class,
            ImportColumn::class,
            Number::class,
            $this->getModelFqn(),
        ];
    }

    public function getBasename(): string
    {
        return class_basename($this->getFqn());
    }

    public function getExtends(): string
    {
        return Importer::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addModelPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetColumnsMethodToClass($class);
        $this->addResolveRecordMethodToClass($class);
        $this->addGetCompletedNotificationBodyMethodToClass($class);
    }

    protected function addModelPropertyToClass(ClassType $class): void
    {
        $property = $class->addProperty('model', new Literal("{$this->simplifyFqn($this->getModelFqn())}::class"))
            ->setProtected()
            ->setStatic()
            ->setType('?string');
        $this->configureModelProperty($property);
    }

    protected function configureModelProperty(Property $property): void {}

    protected function addGetColumnsMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getColumns')
            ->setPublic()
            ->setStatic()
            ->setReturnType('array')
            ->setBody(<<<PHP
                return [
                    {$this->outputImporterColumns()}
                ];
                PHP);

        $this->configureGetColumnsMethod($method);
    }

    public function outputImporterColumns(): string
    {
        $columns = $this->getImporterColumns();

        if (empty($columns)) {
            return '//';
        }

        return implode(PHP_EOL . '    ', $columns);
    }

    /**
     * @return array<string>
     */
    public function getImporterColumns(): array
    {
        if (! $this->isGenerated()) {
            return [];
        }

        $model = $this->getModelFqn();

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

        return array_map(
            function (array $columnData, string $columnName): string {
                $column = (string) new Literal("{$this->simplifyFqn(ImportColumn::class)}::make(?)", [$columnName]);

                foreach ($columnData as $methodName => $parameters) {
                    $column .= new Literal(PHP_EOL . "        ->{$methodName}(...?:)", [$parameters]);
                }

                return "{$column},";
            },
            $columns,
            array_keys($columns),
        );
    }

    protected function configureGetColumnsMethod(Method $method): void {}

    protected function addResolveRecordMethodToClass(ClassType $class): void
    {
        $resolutionMode = $this->getResolutionMode();
        $resolutionColumn = $this->getResolutionColumn();

        $method = $class->addMethod('resolveRecord')
            ->setPublic()
            ->setReturnType(match ($resolutionMode) {
                'update' => "?{$this->getModelFqn()}",
                default => $this->getModelFqn(),
            })
            ->setBody(match ($resolutionMode) {
                'upsert' => new Literal(<<<PHP
                    return {$this->simplifyFqn($this->getModelFqn())}::firstOrNew([
                        ? => \$this->data[?],
                    ]);
                    PHP, [$resolutionColumn, $resolutionColumn]),
                'update' => new Literal(<<<PHP
                    return {$this->simplifyFqn($this->getModelFqn())}::query()
                        ->where(?, \$this->data[?])
                        ->first();
                    PHP, [$resolutionColumn, $resolutionColumn]),
                default => <<<PHP
                    return new {$this->simplifyFqn($this->getModelFqn())}();
                    PHP,
            });

        $this->configureResolveRecordMethod($method);
    }

    protected function configureResolveRecordMethod(Method $method): void {}

    protected function addGetCompletedNotificationBodyMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getCompletedNotificationBody')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(<<<PHP
                \$body = 'Your {$this->getModelLabel()} import has completed and ' . {$this->simplifyFqn(Number::class)}::format(\$import->successful_rows) . ' ' . str('row')->plural(\$import->successful_rows) . ' imported.';

                if (\$failedRowsCount = \$import->getFailedRowsCount()) {
                    \$body .= ' ' . {$this->simplifyFqn(Number::class)}::format(\$failedRowsCount) . ' ' . str('row')->plural(\$failedRowsCount) . ' failed to import.';
                }

                return \$body;
                PHP);
        $method->addParameter('import')
            ->setType(Import::class);

        $this->configureGetCompletedNotificationBodyMethod($method);
    }

    protected function configureGetCompletedNotificationBodyMethod(Method $method): void {}

    public function getFqn(): string
    {
        return $this->fqn;
    }

    public function getModelLabel(): string
    {
        return get_model_label($this->getModelFqn());
    }

    /**
     * @return class-string<Model>
     */
    public function getModelFqn(): string
    {
        return $this->modelFqn;
    }

    public function getResolutionMode(): string
    {
        return $this->resolutionMode;
    }

    public function getResolutionColumn(): ?string
    {
        return $this->resolutionColumn;
    }

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }
}
