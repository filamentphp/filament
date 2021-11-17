<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    protected $displayColumnName = null;

    protected $isPreloaded = false;

    protected $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BelongsToSelect $component): void {
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

    public function preload(bool | callable $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function relationship(string | callable $relationshipName, string | callable $displayColumnName, ?callable $callback = null): static
    {
        $this->displayColumnName = $displayColumnName;
        $this->relationship = $relationshipName;

        $this->getOptionLabelUsing(function (BelongsToSelect $component, $value) {
            $relationship = $component->getRelationship();

            $record = $relationship->getRelated()->where($relationship->getOwnerKeyName(), $value)->first();

            return $record ? $record->getAttributeValue($component->getDisplayColumnName()) : null;
        });

        $this->getSearchResultsUsing(function (BelongsToSelect $component, ?string $query) use ($callback): array {
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
                ->pluck($component->getDisplayColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->options(function (BelongsToSelect $component) use ($callback): array {
            if ($component->isSearchable() && ! $component->isPreloaded()) {
                return [];
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $callback($relationshipQuery);
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->exists(
            fn (BelongsToSelect $component): string => get_class($component->getRelationship()->getModel()),
            fn (BelongsToSelect $component): string => $component->getRelationship()->getOwnerKeyName(),
        );

        return $this;
    }

    public function saveRelationships(): void
    {
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

    public function getRelationship(): BelongsTo
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
