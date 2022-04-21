<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    protected string | Closure | null $displayColumnName = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected bool | Closure $isPreloaded = false;

    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (BelongsToSelect $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();
            $relatedModel = $relationship->getResults();

            if (! $relatedModel) {
                return;
            }

            $component->state(
                $relatedModel->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->saveRelationshipsUsing(static function (BelongsToSelect $component, Model $record, $state) {
            $component->getRelationship()->associate($state);
            $record->save();
        });
    }

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function getSearchColumns(): array
    {
        return $this->searchColumns ?? [$this->getDisplayColumnName()];
    }

    public function relationship(string | Closure $relationshipName, string | Closure $displayColumnName, ?Closure $callback = null): static
    {
        $this->displayColumnName = $displayColumnName;
        $this->relationship = $relationshipName;

        $this->getOptionLabelUsing(static function (BelongsToSelect $component, $value) {
            $relationship = $component->getRelationship();

            $record = $relationship->getRelated()->query()->where($relationship->getOwnerKeyName(), $value)->first();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            return $record->getAttributeValue($component->getDisplayColumnName());
        });

        $this->getSearchResultsUsing(static function (BelongsToSelect $component, ?string $searchQuery) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            $searchQuery = strtolower($searchQuery);

            $relationshipQuery = $component->applySearchConstraint($relationshipQuery, $searchQuery)->limit(50);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(fn (Model $record) => [
                        $record->{$relationship->getOwnerKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->options(static function (BelongsToSelect $component) use ($callback): array {
            if ($component->isSearchable() && ! $component->isPreloaded()) {
                return [];
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(fn (Model $record) => [
                        $record->{$relationship->getOwnerKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->exists(
            fn (BelongsToSelect $component): ?string => ($relationship = $component->getRelationship()) ? $relationship->getModel()::class : null,
            fn (BelongsToSelect $component): string => $component->getRelationship()->getOwnerKeyName(),
        );

        return $this;
    }

    protected function applySearchConstraint(Builder $query, string $searchQuery): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $isFirst = true;

        foreach ($this->getSearchColumns() as $searchColumnName) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->{$whereClause}(
                $searchColumnName,
                $searchOperator,
                "%{$searchQuery}%",
            );

            $isFirst = false;
        }

        return $query;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string
    {
        return $this->evaluate($this->getOptionLabelFromRecordUsing, ['record' => $record]);
    }

    public function getDisplayColumnName(): string
    {
        return $this->evaluate($this->displayColumnName);
    }

    public function getLabel(): string
    {
        if ($this->label === null) {
            return (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getRelationship(): ?BelongsTo
    {
        return $this->getModelInstance()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }

    public function isPreloaded(): bool
    {
        return $this->evaluate($this->isPreloaded);
    }

    public function hasDynamicOptions(): bool
    {
        return $this->isPreloaded();
    }
}
