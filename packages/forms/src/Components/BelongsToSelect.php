<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    protected string | Closure | null $displayColumnName = null;

    protected bool | Closure $isPreloaded = false;

    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BelongsToSelect $component): void {
            if (filled($this->getState())) {
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

        $this->getOptionLabelUsing(function (BelongsToSelect $component, $value) {
            $relationship = $component->getRelationship();

            $record = $relationship->getRelated()->query()->where($relationship->getOwnerKeyName(), $value)->first();

            return $record?->getAttributeValue($component->getDisplayColumnName());
        });

        $this->getSearchResultsUsing(function (BelongsToSelect $component, ?string $query) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $this->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            $query = strtolower($query);

            /** @var Connection $databaseConnection */
            $databaseConnection = $relationshipQuery->getConnection();

            $searchOperator = match ($databaseConnection->getDriverName()) {
                'pgsql' => 'ilike',
                default => 'like',
            };

            return $relationshipQuery
                ->where($component->getDisplayColumnName(), $searchOperator, "%{$query}%")
                ->limit(50)
                ->pluck($component->getDisplayColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->options(function (BelongsToSelect $component) use ($callback): array {
            if ($component->isSearchable() && ! $component->isPreloaded()) {
                return [];
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $this->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
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

    public function saveRelationships(): void
    {
        if ($this->saveRelationshipsUsing) {
            parent::saveRelationships();

            return;
        }

        $this->getRelationship()->associate($this->getState());
        $this->getModel()->save();
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
        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        if (is_string($model)) {
            $model = new $model();
        }

        return $model->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }

    public function isPreloaded(): bool
    {
        return $this->evaluate($this->isPreloaded);
    }
}
