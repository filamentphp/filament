<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BelongsToManyMultiSelect extends MultiSelect
{
    protected $displayColumnName;

    protected $isPreloaded = false;

    protected $relationship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BelongsToManyMultiSelect $component): void {
            $relationship = $component->getRelationship();
            $relatedModels = $relationship->getResults();

            if (! $relatedModels) {
                return;
            }

            $component->state(
                $relatedModels->pluck(
                    $relationship->getRelatedKeyName(),
                )->toArray(),
            );
        });

        $this->dehydrated(false);
    }

    public function preload(bool | callable $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function relationship(string | callable $relationshipName, string | callable $displayColumnName, ?callable $callback = null): static
    {
        $this->displayColumnName = $displayColumnName;
        $this->relationship = $relationshipName;

        $this->getOptionLabelsUsing(function (BelongsToManyMultiSelect $component, array $values): array {
            $relationship = $component->getRelationship();
            $relatedKeyName = $relationship->getRelatedKeyName();

            return $relationship->getRelated()
                ->whereIn($relatedKeyName, $values)
                ->pluck($component->getDisplayColumnName(), $relatedKeyName)
                ->toArray();
        });

        $this->getSearchResultsUsing(function (BelongsToManyMultiSelect $component, ?string $query) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $callback($relationshipQuery);
            }

            $query = strtolower($query);
            $searchOperator = match ($relationshipQuery->getConnection()->getDriverName()) {
                'pgsql' => 'ilike',
                default => 'like',
            };

            return $relationshipQuery
                ->where($component->getDisplayColumnName(), $searchOperator, "%{$query}%")
                ->pluck($component->getDisplayColumnName(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        $this->options(function (BelongsToManyMultiSelect $component) use ($callback): array {
            if (! $component->isPreloaded()) {
                return [];
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $callback($relationshipQuery);
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        return $this;
    }

    public function saveRelationships(): void
    {
        $this->getRelationship()->sync($this->getState());
    }

    public function getDisplayColumnName(): string
    {
        return $this->evaluate($this->displayColumnName);
    }

    public function getLabel(): string
    {
        if ($this->label) {
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
        $model = $this->getModel();

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
