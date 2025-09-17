<?php

namespace Filament\Actions\Commands\FileGenerators;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
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

class ExporterClassGenerator extends ClassGenerator
{
    use CanReadModelSchemas;

    /**
     * @param  class-string<Model>  $modelFqn
     */
    final public function __construct(
        protected string $fqn,
        protected string $modelFqn,
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
            Export::class,
            ExportColumn::class,
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
        return Exporter::class;
    }

    protected function addPropertiesToClass(ClassType $class): void
    {
        $this->addModelPropertyToClass($class);
    }

    protected function addMethodsToClass(ClassType $class): void
    {
        $this->addGetColumnsMethodToClass($class);
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
                    {$this->outputExporterColumns()}
                ];
                PHP);

        $this->configureGetColumnsMethod($method);
    }

    public function outputExporterColumns(): string
    {
        $columns = $this->getExporterColumns();

        if (empty($columns)) {
            return '//';
        }

        return implode(PHP_EOL . '    ', $columns);
    }

    /**
     * @return array<string>
     */
    public function getExporterColumns(): array
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
            $columnName = $column['name'];

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

            $columns[$columnName] = $columnData;
        }

        return array_map(
            function (array $columnData, string $columnName): string {
                $column = (string) new Literal("{$this->simplifyFqn(ExportColumn::class)}::make(?)", [$columnName]);

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

    protected function addGetCompletedNotificationBodyMethodToClass(ClassType $class): void
    {
        $method = $class->addMethod('getCompletedNotificationBody')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody(<<<PHP
                \$body = 'Your {$this->getModelLabel()} export has completed and ' . {$this->simplifyFqn(Number::class)}::format(\$export->successful_rows) . ' ' . str('row')->plural(\$export->successful_rows) . ' exported.';

                if (\$failedRowsCount = \$export->getFailedRowsCount()) {
                    \$body .= ' ' . {$this->simplifyFqn(Number::class)}::format(\$failedRowsCount) . ' ' . str('row')->plural(\$failedRowsCount) . ' failed to export.';
                }

                return \$body;
                PHP);
        $method->addParameter('export')
            ->setType(Export::class);

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

    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }
}
