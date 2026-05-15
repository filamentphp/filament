<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Support\Arr;
use LogicException;
use Znck\Eloquent\Relations\BelongsToThrough;

class TableSelect extends Field
{
    use Concerns\CanLimitItemsLength;
    use Concerns\HasPivotData;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.table-select';

    protected string | Closure | null $tableConfiguration = null;

    protected bool | Closure $shouldIgnoreRelatedRecords = false;

    protected string | Closure | null $relationship = null;

    protected bool | Closure $isMultiple = false;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $tableArguments = [];

    public function tableConfiguration(string | Closure $tableConfiguration): static
    {
        $this->tableConfiguration = $tableConfiguration;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $arguments
     */
    public function tableArguments(array | Closure $arguments): static
    {
        $this->tableArguments = $arguments;

        return $this;
    }

    public function ignoreRelatedRecords(bool | Closure $condition = true): static
    {
        $this->shouldIgnoreRelatedRecords = $condition;

        return $this;
    }

    public function shouldIgnoreRelatedRecords(): bool
    {
        return (bool) $this->evaluate($this->shouldIgnoreRelatedRecords);
    }

    public function getTableConfiguration(): string
    {
        return $this->evaluate($this->tableConfiguration) ?? throw new LogicException('The [tableConfiguration()] method must be set when using a [TableSelect] component.');
    }

    /**
     * @return array<mixed>
     */
    public function getTableArguments(): array
    {
        return $this->evaluate($this->tableArguments) ?? [];
    }

    public function relationship(string | Closure | null $name): static
    {
        $this->relationshipName($name);

        $this->loadStateFromRelationshipsUsing(static function (TableSelect $component): void {
            $component->fillStateFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (TableSelect $component): void {
            $component->saveStateToRelationship();
        });

        $this->dehydrated(fn (TableSelect $component): bool => (! $component->isMultiple()) && $component->isSaved());

        return $this;
    }

    public function fillStateFromRelationship(): void
    {
        if (filled($this->getState())) {
            return;
        }

        $relationship = $this->getRelationship();
        $relationshipName = $this->getRelationshipName();

        if (
            (! str_contains($relationshipName, '.')) &&
            ($record = $this->getRecord()) instanceof Model &&
            $record->relationLoaded($relationshipName)
        ) {
            $relatedRecords = $record->getRelationValue($relationshipName);

            if (
                ($relationship instanceof BelongsToMany) ||
                ($relationship instanceof HasOneOrManyThrough)
            ) {
                $this->state(
                    $relatedRecords
                        ->pluck(($relationship instanceof BelongsToMany) ? $relationship->getRelatedKeyName() : $relationship->getRelated()->getKeyName())
                        ->map(static fn ($key): string => strval($key))
                        ->all(),
                );

                return;
            }

            if ($relationship instanceof BelongsToThrough) {
                $this->state(
                    $relatedRecords?->getAttribute(
                        $relationship->getRelated()->getKeyName(),
                    ),
                );

                return;
            }

            if ($relationship instanceof HasMany) {
                $this->state(
                    $relatedRecords
                        ->pluck($relationship->getLocalKeyName())
                        ->all(),
                );

                return;
            }

            if ($relationship instanceof HasOne) {
                $this->state(
                    $relatedRecords?->getAttribute(
                        $relationship->getLocalKeyName(),
                    ),
                );

                return;
            }

            /** @var BelongsTo $relationship */
            $this->state(
                $relatedRecords?->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );

            return;
        }

        if (
            ($relationship instanceof BelongsToMany) ||
            ($relationship instanceof HasOneOrManyThrough)
        ) {
            /** @var Collection $relatedRecords */
            $relatedRecords = $relationship->getResults();

            $this->state(
                // Cast the related keys to a string, otherwise
                // JavaScript can't handle deselection.
                //
                // https://github.com/filamentphp/filament/issues/1111
                $relatedRecords
                    ->pluck(($relationship instanceof BelongsToMany) ? $relationship->getRelatedKeyName() : $relationship->getRelated()->getKeyName())
                    ->map(static fn ($key): string => strval($key))
                    ->all(),
            );

            return;
        }

        if ($relationship instanceof BelongsToThrough) {
            /** @var ?Model $relatedModel */
            $relatedModel = $relationship->getResults();

            $this->state(
                $relatedModel?->getAttribute(
                    $relationship->getRelated()->getKeyName(),
                ),
            );

            return;
        }

        if ($relationship instanceof HasMany) {
            /** @var Collection $relatedRecords */
            $relatedRecords = $relationship->getResults();

            $this->state(
                $relatedRecords
                    ->pluck($relationship->getLocalKeyName())
                    ->all(),
            );

            return;
        }

        if ($relationship instanceof HasOne) {
            $relatedModel = $relationship->getResults();

            $this->state(
                $relatedModel?->getAttribute(
                    $relationship->getLocalKeyName(),
                ),
            );

            return;
        }

        /** @var BelongsTo $relationship */
        $relatedModel = $relationship->getResults();

        $this->state(
            $relatedModel?->getAttribute(
                $relationship->getOwnerKeyName(),
            ),
        );
    }

    public function saveStateToRelationship(): void
    {
        $relationship = $this->getRelationship();
        $record = $this->getRecord();
        $relationshipName = $this->getRelationshipName();
        $state = $this->getState();

        if (($relationship instanceof HasOne) || ($relationship instanceof HasMany)) {
            $query = $relationship->getQuery();

            $query->update([
                $relationship->getForeignKeyName() => null,
            ]);

            if (! empty($state)) {
                $relationship::noConstraints(function () use ($record, $state): void {
                    $relationship = $this->getRelationship();

                    $query = $relationship->getQuery()->whereIn($relationship->getLocalKeyName(), Arr::wrap($state));

                    $query->update([
                        $relationship->getForeignKeyName() => $record->getAttribute($relationship->getLocalKeyName()),
                    ]);
                });
            }

            $record->unsetRelation($relationshipName);

            return;
        }

        if (
            ($relationship instanceof HasOneOrMany) ||
            ($relationship instanceof HasOneOrManyThrough) ||
            ($relationship instanceof BelongsToThrough)
        ) {
            return;
        }

        if (! $relationship instanceof BelongsToMany) {
            // Security: If the model is new and the foreign key is already
            // filled, don't overwrite it — the key may have been set by
            // authorization logic or event listeners before save.
            if (
                $record->wasRecentlyCreated &&
                filled($record->getAttributeValue($relationship->getForeignKeyName()))
            ) {
                return;
            }

            $relationship->associate($state);
            $record->wasRecentlyCreated && $record->save();

            return;
        }

        /** @var Collection $relatedRecords */
        $relatedRecords = $relationship->getResults();

        $state = Arr::wrap($state ?? []);

        $recordsToDetach = array_diff(
            $relatedRecords
                ->pluck($relationship->getRelatedKeyName())
                ->map(static fn ($key): string => strval($key))
                ->all(),
            $state,
        );

        if (count($recordsToDetach) > 0) {
            $relationship->detach($recordsToDetach);
        }

        $pivotData = $this->getPivotData();

        if ($pivotData === []) {
            $relationship->sync($state, detaching: false);
            $record->unsetRelation($relationshipName);

            return;
        }

        $relationship->syncWithPivotValues($state, $pivotData, detaching: false);
        $record->unsetRelation($relationshipName);
    }

    public function relationshipName(string | Closure | null $name): static
    {
        $this->relationship = $name;

        return $this;
    }

    public function getRelationship(): BelongsTo | BelongsToMany | HasOneOrMany | HasOneOrManyThrough | BelongsToThrough | null
    {
        if (! $this->hasRelationship()) {
            return null;
        }

        $record = $this->getModelInstance();

        $relationship = null;

        $relationshipName = $this->getRelationshipName();

        foreach (explode('.', $relationshipName) as $nestedRelationshipName) {
            if ($record->hasAttribute($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        if (! $relationship) {
            throw new LogicException("The relationship [{$relationshipName}] does not exist on the model [{$this->getModel()}].");
        }

        return $relationship;
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->hasRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts()) {
            return parent::getDefaultStateCasts();
        }

        if ($this->isMultiple()) {
            return [app(OptionsArrayStateCast::class)];
        }

        return [app(OptionStateCast::class, ['isNullable' => true])];
    }
}
