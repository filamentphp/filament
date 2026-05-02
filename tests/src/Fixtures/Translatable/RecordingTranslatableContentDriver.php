<?php

namespace Filament\Tests\Fixtures\Translatable;

use Filament\Support\Contracts\TranslatableContentDriver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecordingTranslatableContentDriver implements TranslatableContentDriver
{
    /** @var array<int, string> */
    public static array $callLog = [];

    public function __construct(string $activeLocale) {}

    public static function reset(): void
    {
        static::$callLog = [];
    }

    public function isAttributeTranslatable(string $model, string $attribute): bool
    {
        return false;
    }

    public function getRecordAttributesToArray(Model $record): array
    {
        return $record->attributesToArray();
    }

    public function makeRecord(string $model, array $data): Model
    {
        static::$callLog[] = 'makeRecord:' . ($data['title'] ?? '');

        $record = new $model;
        $record->fill($data);

        return $record;
    }

    public function setRecordLocale(Model $record): Model
    {
        return $record;
    }

    public function updateRecord(Model $record, array $data): Model
    {
        static::$callLog[] = 'updateRecord:' . ($data['title'] ?? '');

        $record->fill($data)->save();

        return $record;
    }

    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isSearchForcedCaseInsensitive = null): Builder
    {
        return $query;
    }
}
