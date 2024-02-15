<?php

namespace Filament\Support\Commands\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

trait CanReadModelSchemas
{
    protected function getModel(string $model): ?string
    {
        if (! class_exists($model)) {
            return null;
        }

        return $model;
    }

    protected function getModelSchema(string $model): Schema
    {
        return app($model)
            ->getConnection()
            ->getSchemaBuilder();
    }

    protected function getModelTable(string $model): string
    {
        return app($model)->getTable();
    }

    protected function guessBelongsToRelationshipName(string $column, string $model): ?string
    {
        /** @var Model $modelInstance */
        $modelInstance = app($model);
        $modelInstanceReflection = new ReflectionClass($modelInstance);
        $guessedRelationshipName = str($column)->beforeLast('_id');
        $hasRelationship = $modelInstanceReflection->hasMethod($guessedRelationshipName);

        if (! $hasRelationship) {
            $guessedRelationshipName = $guessedRelationshipName->camel();
            $hasRelationship = $modelInstanceReflection->hasMethod($guessedRelationshipName);
        }

        if (! $hasRelationship) {
            return null;
        }

        try {
            $type = $modelInstanceReflection->getMethod($guessedRelationshipName)->getReturnType();

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

    protected function guessBelongsToRelationshipTableName(string $column): ?string
    {
        $tableName = str($column)->beforeLast('_id');

        if (Schema::hasTable(Str::plural($tableName))) {
            return Str::plural($tableName);
        }

        if (! Schema::hasTable($tableName)) {
            return null;
        }

        return $tableName;
    }

    protected function guessBelongsToRelationshipTitleColumnName(string $column, string $model): string
    {
        $schema = $this->getModelSchema($model);
        $table = $this->getModelTable($model);

        $columns = collect($schema->getColumnListing($table));

        if ($columns->contains('name')) {
            return 'name';
        }

        if ($columns->contains('title')) {
            return 'title';
        }

        return collect($schema->getIndexes($table))->firstWhere('primary')['columns'][0] ?? 'id';
    }

    protected function parseColumnType(array $column): array
    {
        $type = match ($column['type']) {
            'tinyint(1)', 'bit' => 'boolean',
            'varchar(max)', 'nvarchar(max)' => 'text',
            default => null,
        };

        $type ??= match ($column['type_name']) {
            'boolean', 'bool' => 'boolean',
            'char', 'bpchar', 'nchar' => 'char',
            'varchar', 'nvarchar' => 'string',
            'integer', 'int', 'int4', 'tinyint', 'smallint', 'int2', 'mediumint', 'bigint', 'int8' => 'integer',
            'decimal', 'numeric' => 'decimal',
            'float', 'real', 'float4' => 'float',
            'double', 'float8' => 'double',
            'money', 'smallmoney' => 'money',
            'date' => 'date',
            'time', 'timetz' => 'time',
            'datetime', 'datetime2', 'smalldatetime', 'datetimeoffset' => 'datetime',
            'timestamp', 'timestamptz' => 'timestamp',
            'text', 'tinytext', 'longtext', 'mediumtext', 'ntext' => 'text',
            'json', 'jsonb' => 'json',

            // 'binary', 'varbinary', 'bytea', 'image', 'blob', 'tinyblob', 'mediumblob', 'longblob' => 'binary',
            // 'uuid', 'uniqueidentifier' => 'uuid',
            // 'enum' => 'enum',
            // 'set' => 'set',
            // 'inet', 'cidr', 'macaddr', 'macaddr8' => 'string',
            // 'bit', 'varbit' => 'bit',
            // 'xml' => 'xml',
            // 'year' => 'year',
            // 'interval' => 'interval',
            // 'geometry', 'geometrycollection', 'linestring', 'multilinestring', 'multipoint', 'multipolygon', 'point', 'polygon', 'box', 'circle', 'line', 'lseg', 'path' => 'geometry',
            // 'geography' => 'geography',
            // 'tsvector', 'tsquery' => 'text',
            default => $column['type_name'],
        };

        $values = str_contains($column['type'], '(')
            ? str_getcsv(Str::between($column['type'], '(', ')'), ',', "'")
            : null;

        $values = is_null($values) ? [] : match ($type) {
            'string', 'char', 'binary', 'bit' => ['length' => (int) $values[0]],
            // 'enum', 'set' => ['values' => $values],
            // 'float', 'decimal', 'double' => ['precision' => (int) $values[0], 'scale' => isset($values[1]) ? (int) $values[1] : null],
            // 'datetime', 'timestamp', 'time', 'interval' => ['precision' => (int) $values[0]],
            // 'geometry', 'geography' => ['subtype' => $values[0] ?? $column['type_name'] ?? null, 'srid' => isset($values[1]) ? (int) $values[1] : null],
            default => [],
        };

        return array_merge(['name' => $type], array_filter($values));
    }
}
