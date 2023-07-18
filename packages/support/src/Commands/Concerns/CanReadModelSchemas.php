<?php

namespace Filament\Support\Commands\Concerns;

use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionException;
use Throwable;

trait CanReadModelSchemas
{
    protected function getModel(string $model): ?string
    {
        if (! class_exists($model)) {
            return null;
        }

        return $model;
    }

    protected function getModelTable(string $model): ?Table
    {
        $modelClass = $model;
        $model = app($model);

        try {
            return $model
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableDetails($model->getTable());
        } catch (Throwable $exception) {
            $this->components->warn("Unable to read table schema for model [{$modelClass}]: {$exception->getMessage()}");

            return null;
        }
    }

    protected function guessBelongsToRelationshipName(AbstractAsset $column, string $model): ?string
    {
        /** @var Model $modelInstance */
        $modelInstance = app($model);
        $modelInstanceReflection = invade($modelInstance);
        $guessedRelationshipName = str($column->getName())->beforeLast('_id');
        $hasRelationship = $modelInstanceReflection->reflected->hasMethod($guessedRelationshipName);

        if (! $hasRelationship) {
            $guessedRelationshipName = $guessedRelationshipName->camel();
            $hasRelationship = $modelInstanceReflection->reflected->hasMethod($guessedRelationshipName);
        }

        if (! $hasRelationship) {
            return null;
        }

        try {
            $type = $modelInstanceReflection->reflected->getMethod($guessedRelationshipName)->getReturnType();

            if (
                (! $type) ||
                (! method_exists($type, 'getName')) ||
                ($type->getName() !== BelongsTo::class)
            ) {
                return null;
            }
        } catch (ReflectionException $exception) {
            return null;
        }

        return $guessedRelationshipName;
    }

    protected function guessBelongsToRelationshipTableName(AbstractAsset $column): ?string
    {
        $tableName = str($column->getName())->beforeLast('_id');

        if (Schema::hasTable(Str::plural($tableName))) {
            return Str::plural($tableName);
        }

        if (! Schema::hasTable($tableName)) {
            return null;
        }

        return $tableName;
    }

    protected function guessBelongsToRelationshipTitleColumnName(AbstractAsset $column, string $model): string
    {
        $schema = $this->getModelTable($model);

        if ($schema === null) {
            return 'id';
        }

        $columns = collect(array_keys($schema->getColumns()));

        if ($columns->contains('name')) {
            return 'name';
        }

        if ($columns->contains('title')) {
            return 'title';
        }

        return $schema->getPrimaryKey()->getColumns()[0];
    }
}
