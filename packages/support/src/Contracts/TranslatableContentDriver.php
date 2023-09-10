<?php

namespace Filament\Support\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface TranslatableContentDriver
{
    public function __construct(string $activeLocale);

    public function isAttributeTranslatable(string $model, string $attribute): bool;

    /**
     * @return array<string, mixed>
     */
    public function getRecordAttributesToArray(Model $record): array;

    /**
     * @param  array<string, mixed>  $data
     */
    public function makeRecord(string $model, array $data): Model;

    public function setRecordLocale(Model $record): Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(Model $record, array $data): Model;

    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, bool $isCaseInsensitivityForced = false): Builder;
}
