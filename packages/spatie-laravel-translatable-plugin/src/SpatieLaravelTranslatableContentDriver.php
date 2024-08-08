<?php

namespace Filament;

use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function Filament\Support\generate_search_column_expression;

class SpatieLaravelTranslatableContentDriver implements TranslatableContentDriver
{
    public function __construct(protected string $activeLocale) {}

    public function isAttributeTranslatable(string $model, string $attribute): bool
    {
        $model = app($model);

        if (! method_exists($model, 'isTranslatableAttribute')) {
            return false;
        }

        return $model->isTranslatableAttribute($attribute);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function makeRecord(string $model, array $data): Model
    {
        $record = new $model;

        if (method_exists($record, 'setLocale')) {
            $record->setLocale($this->activeLocale);
        }

        $translatableAttributes = method_exists($record, 'getTranslatableAttributes') ?
            $record->getTranslatableAttributes() :
            [];

        $record->fill(Arr::except($data, $translatableAttributes));

        if (method_exists($record, 'setTranslation')) {
            foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
                $record->setTranslation($key, $this->activeLocale, $value);
            }
        }

        return $record;
    }

    public function setRecordLocale(Model $record): Model
    {
        if (! method_exists($record, 'setLocale')) {
            return $record;
        }

        return $record->setLocale($this->activeLocale);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(Model $record, array $data): Model
    {
        if (method_exists($record, 'setLocale')) {
            $record->setLocale($this->activeLocale);
        }

        $translatableAttributes = method_exists($record, 'getTranslatableAttributes') ?
            $record->getTranslatableAttributes() :
            [];

        $record->fill(Arr::except($data, $translatableAttributes));

        if (method_exists($record, 'setTranslation')) {
            foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
                $record->setTranslation($key, $this->activeLocale, $value);
            }
        }

        $record->save();

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRecordAttributesToArray(Model $record): array
    {
        $attributes = $record->attributesToArray();

        if (! method_exists($record, 'getTranslatableAttributes')) {
            return $attributes;
        }

        if (! method_exists($record, 'getTranslation')) {
            return $attributes;
        }

        foreach ($record->getTranslatableAttributes() as $attribute) {
            $attributes[$attribute] = $record->getTranslation($attribute, $this->activeLocale, useFallbackLocale: false);
        }

        return $attributes;
    }

    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isCaseInsensitivityForced = null): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $column = match ($databaseConnection->getDriverName()) {
            'pgsql' => "{$column}->>'{$this->activeLocale}'",
            default => "json_extract({$column}, \"$.{$this->activeLocale}\")",
        };

        return $query->{$whereClause}(
            generate_search_column_expression($column, $isCaseInsensitivityForced, $databaseConnection),
            'like',
            (string) str($search)->wrap('%'),
        );
    }
}
