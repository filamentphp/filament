<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BelongsToManyMultiSelect extends MultiSelect
{
    protected string | Closure | null $displayColumnName = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected bool | Closure $isPreloaded = false;

    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (BelongsToManyMultiSelect $component, ?array $state): void {
            if (count($state ?? [])) {
                return;
            }

            $relationship = $component->getRelationship();
            $relatedModels = $relationship->getResults();

            $component->state(
                // Cast the related keys to a string, otherwise JavaScript does not
                // know how to handle deselection.
                //
                // https://github.com/laravel-filament/filament/issues/1111
                $relatedModels
                    ->pluck($relationship->getRelatedKeyName())
                    ->map(fn ($key): string => strval($key))
                    ->toArray(),
            );
        });

        $this->saveRelationshipsUsing(static function (BelongsToManyMultiSelect $component, ?array $state) {
            $component->getRelationship()->sync($state ?? []);
        });

        $this->dehydrated(false);
    }

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function relationship(string | Closure $relationshipName, string | Closure $displayColumnName, ?Closure $callback = null): static
    {
        $this->displayColumnName = $displayColumnName;
        $this->relationship = $relationshipName;

        $this->getOptionLabelsUsing(static function (BelongsToManyMultiSelect $component, array $values): array {
            $relationship = $component->getRelationship();
            $relatedKeyName = $relationship->getRelatedKeyName();

            $relationshipQuery = $relationship->getRelated()->query()
                ->whereIn($relatedKeyName, $values);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relatedKeyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relatedKeyName)
                ->toArray();
        });

        $this->getSearchResultsUsing(static function (BelongsToManyMultiSelect $component, ?string $searchQuery) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            $searchQuery = strtolower($searchQuery);

            /** @var Connection $databaseConnection */
            $databaseConnection = $relationshipQuery->getConnection();

            $searchOperator = match ($databaseConnection->getDriverName()) {
                'pgsql' => 'ilike',
                default => 'like',
            };

            $relationshipQuery = $relationshipQuery
                ->where($component->getDisplayColumnName(), $searchOperator, "%{$searchQuery}%")
                ->limit(50);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(fn (Model $record) => [
                        $record->{$relationship->getRelatedKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        $this->options(static function (BelongsToManyMultiSelect $component) use ($callback): array {
            if (! $component->isPreloaded()) {
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
                        $record->{$relationship->getRelatedKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        return $this;
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

    public function getRelationship(): BelongsToMany
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
