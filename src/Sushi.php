<?php

namespace Filament;

use DateTime;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Str;
use ReflectionClass;

trait Sushi
{
    protected static $sushiConnection;

    public static function bootSushi()
    {
        $instance = (new static);

        $cacheFileName = config('sushi.cache-prefix', 'sushi') . '-' . Str::kebab(str_replace('\\', '', static::class)) . '.sqlite';
        $cacheDirectory = realpath(config('sushi.cache-path', storage_path('framework/cache')));
        $cachePath = $cacheDirectory . '/' . $cacheFileName;
        $modelPath = (new ReflectionClass(static::class))->getFileName();

        $states = [
            'cache-file-found-and-up-to-date' => function () use ($cachePath) {
                static::setSqliteConnection($cachePath);
            },
            'cache-file-not-found-or-stale' => function () use ($cachePath, $modelPath, $instance) {
                file_put_contents($cachePath, '');

                static::setSqliteConnection($cachePath);

                $instance->migrate();

                touch($cachePath, filemtime($modelPath));
            },
            'no-caching-capabilities' => function () use ($instance) {
                static::setSqliteConnection(':memory:');

                $instance->migrate();
            },
        ];

        switch (true) {
            case ! property_exists($instance, 'rows'):
                $states['no-caching-capabilities']();
                break;

            case file_exists($cachePath) && filemtime($modelPath) <= filemtime($cachePath):
                $states['cache-file-found-and-up-to-date']();
                break;

            case file_exists($cacheDirectory) && is_writable($cacheDirectory):
                $states['cache-file-not-found-or-stale']();
                break;

            default:
                $states['no-caching-capabilities']();
                break;
        }
    }

    public static function resolveConnection($connection = null)
    {
        return static::$sushiConnection;
    }

    protected static function setSqliteConnection($database)
    {
        static::$sushiConnection = app(ConnectionFactory::class)->make([
            'driver' => 'sqlite',
            'database' => $database,
        ]);
    }

    public function migrate()
    {
        $rows = $this->getRows();
        $tableName = $this->getTable();

        if (count($rows)) {
            $this->createTable($tableName, $rows[0]);
        } else {
            $this->createTableWithNoData($tableName);
        }

        static::insert($rows);
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function createTable(string $tableName, $firstRow)
    {
        static::resolveConnection()->getSchemaBuilder()->create($tableName, function ($table) use ($firstRow) {
            // Add the "id" column if it doesn't already exist in the rows.
            if ($this->incrementing && ! in_array($this->primaryKey, array_keys($firstRow))) {
                $table->increments($this->primaryKey);
            }

            foreach ($firstRow as $column => $value) {
                switch (true) {
                    case is_int($value):
                        $type = 'integer';
                        break;
                    case is_numeric($value):
                        $type = 'float';
                        break;
                    case is_string($value):
                        $type = 'string';
                        break;
                    case is_object($value) && $value instanceof DateTime:
                        $type = 'dateTime';
                        break;
                    default:
                        $type = 'string';
                }

                if ($column === $this->primaryKey && $type == 'integer') {
                    $table->increments($this->primaryKey);
                    continue;
                }

                $schema = $this->getSchema();

                $type = $schema[$column] ?? $type;

                $table->{$type}($column)->nullable();
            }

            if ($this->usesTimestamps() && (! in_array('updated_at', array_keys($firstRow)) || ! in_array('created_at', array_keys($firstRow)))) {
                $table->timestamps();
            }
        });
    }

    public function getSchema()
    {
        return $this->schema ?? [];
    }

    public function usesTimestamps()
    {
        // Override the Laravel default value of $timestamps = true; Unless otherwise set.
        return (new ReflectionClass($this))->getProperty('timestamps')->class === static::class
            ? parent::usesTimestamps()
            : false;
    }

    public function createTableWithNoData(string $tableName)
    {
        static::resolveConnection()->getSchemaBuilder()->create($tableName, function ($table) {
            $schema = $this->schema;

            if ($this->incrementing && ! in_array($this->primaryKey, array_keys($schema))) {
                $table->increments($this->primaryKey);
            }

            foreach ($schema as $name => $type) {
                if ($name === $this->primaryKey && $type == 'integer') {
                    $table->increments($this->primaryKey);
                    continue;
                }

                $table->{$type}($name)->nullable();
            }

            if ($this->usesTimestamps() && (! in_array('updated_at', array_keys($schema)) || ! in_array('created_at', array_keys($schema)))) {
                $table->timestamps();
            }
        });
    }
}
