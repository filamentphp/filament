<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LogicException;
use Znck\Eloquent\Relations\BelongsToThrough;

class ModalTableSelect extends Field
{
    use Concerns\CanLimitItemsLength;
    use Concerns\HasPivotData;
    use Concerns\HasPlaceholder;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.modal-table-select';

    protected ?Model $cachedSelectedRecord = null;

    protected bool | Closure $isMultiple = false;

    protected ?Closure $getOptionLabelUsing;

    protected ?Closure $getSelectedRecordUsing = null;

    protected ?Closure $getOptionLabelsUsing;

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $tableConfiguration = null;

    protected ?Closure $modifyTableSelectUsing = null;

    protected ?Closure $modifySelectActionUsing = null;

    protected bool | Closure | null $hasBadges = null;

    protected string | Closure | null $badgeColor = null;

    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $tableArguments = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            fn (ModalTableSelect $component): Action => $component->getSelectAction(),
        ]);
    }

    public function tableSelect(?Closure $callback): static
    {
        $this->modifyTableSelectUsing = $callback;

        return $this;
    }

    public function selectAction(?Closure $callback): static
    {
        $this->modifySelectActionUsing = $callback;

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

    public function getSelectAction(): Action
    {
        $action = Action::make('select')
            ->label(__('filament-forms::components.modal_table_select.actions.select.label'))
            ->slideOver()
            ->modalHeading($this->getLabel())
            ->modalSubmitActionLabel(__('filament-forms::components.modal_table_select.actions.select.actions.select.label'))
            ->icon(Heroicon::PencilSquare)
            ->iconPosition(IconPosition::After)
            ->fillForm(['selection' => $this->getState()])
            ->schema([$this->getTableSelect()])
            ->action(fn (array $data) => $this->state($data['selection'])->callAfterStateUpdated());

        if ($this->isMultiple() || blank($this->getState())) {
            $action->link();
        } else {
            $action->iconButton();
        }

        if ($this->modifySelectActionUsing) {
            $action = $this->evaluate($this->modifySelectActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function getTableSelect(): TableSelect
    {
        $select = TableSelect::make('selection')
            ->label($this->getLabel())
            ->hiddenLabel()
            ->tableConfiguration($this->getTableConfiguration())
            ->relationshipName($this->getRelationshipName())
            ->multiple($this->isMultiple())
            ->maxItems($this->getMaxItems())
            ->tableArguments($this->getTableArguments());

        if ($this->modifyTableSelectUsing) {
            $select = $this->evaluate(
                $this->modifyTableSelectUsing,
                namedInjections: [
                    'select' => $select,
                    'tableSelect' => $select,
                ],
                typedInjections: [
                    TableSelect::class => $select,
                ],
            ) ?? $select;
        }

        return $select;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getSelectedRecordUsing(?Closure $callback): static
    {
        $this->getSelectedRecordUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function getOptionLabel(bool $withDefault = true): string | Htmlable | null
    {
        $state = null;

        $label = $this->evaluate($this->getOptionLabelUsing, [
            'value' => function () use (&$state): mixed {
                return $state = $this->getState();
            },
        ]);

        if ($withDefault) {
            $label ??= ($state ?? $this->getState());
        }

        return $label;
    }

    /**
     * @return array<string | Htmlable>
     */
    public function getOptionLabels(bool $withDefaults = true): array
    {
        $labels = $this->evaluate($this->getOptionLabelsUsing, [
            'values' => fn (): array => $this->getState(),
        ]);

        if ($labels instanceof Arrayable) {
            $labels = $labels->toArray();
        }

        foreach ($labels as $value => $label) {
            if (filled($label)) {
                continue;
            }

            if ($withDefaults) {
                $labels[$value] = $value;

                continue;
            }

            unset($labels[$value]);
        }

        return $labels;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function relationship(string | Closure | null $name = null, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null, bool $ignoreRecord = false): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;

        $this->loadStateFromRelationshipsUsing(static function (ModalTableSelect $component, $state) use ($modifyQueryUsing): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();
            $relationshipName = $component->getRelationshipName();

            if (
                (! $modifyQueryUsing) &&
                (! str_contains($relationshipName, '.')) &&
                ($record = $component->getRecord()) instanceof Model &&
                $record->relationLoaded($relationshipName)
            ) {
                $relatedRecords = $record->getRelationValue($relationshipName);

                if (
                    ($relationship instanceof BelongsToMany) ||
                    ($relationship instanceof HasOneOrManyThrough)
                ) {
                    $component->state(
                        $relatedRecords
                            ->pluck(($relationship instanceof BelongsToMany) ? $relationship->getRelatedKeyName() : $relationship->getRelated()->getKeyName())
                            ->map(static fn ($key): string => strval($key))
                            ->all(),
                    );

                    return;
                }

                if ($relationship instanceof BelongsToThrough) {
                    $component->state(
                        $relatedRecords?->getAttribute(
                            $relationship->getRelated()->getKeyName(),
                        ),
                    );

                    return;
                }

                if ($relationship instanceof HasMany) {
                    $component->state(
                        $relatedRecords
                            ->pluck($relationship->getLocalKeyName())
                            ->map(static fn ($key): string => strval($key))
                            ->all(),
                    );

                    return;
                }

                if ($relationship instanceof HasOne) {
                    $component->state(
                        $relatedRecords?->getAttribute(
                            $relationship->getLocalKeyName(),
                        ),
                    );

                    return;
                }

                /** @var BelongsTo $relationship */
                $component->state(
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
                if ($modifyQueryUsing) {
                    $component->evaluate($modifyQueryUsing, [
                        'query' => $relationship->getQuery(),
                    ]);
                }

                /** @var Collection $relatedRecords */
                $relatedRecords = $relationship->getResults();

                $component->state(
                    // Cast the related keys to a string, otherwise JavaScript does not
                    // know how to handle deselection.
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

                $component->state(
                    $relatedModel?->getAttribute(
                        $relationship->getRelated()->getKeyName(),
                    ),
                );

                return;
            }

            if ($relationship instanceof HasMany) {
                /** @var Collection $relatedRecords */
                $relatedRecords = $relationship->getResults();

                $component->state(
                    // Cast the related keys to a string, otherwise JavaScript does not
                    // know how to handle deselection.
                    //
                    // https://github.com/filamentphp/filament/issues/1111
                    $relatedRecords
                        ->pluck($relationship->getLocalKeyName())
                        ->map(static fn ($key): string => strval($key))
                        ->all(),
                );

                return;
            }

            if ($relationship instanceof HasOne) {
                $relatedModel = $relationship->getResults();

                $component->state(
                    $relatedModel?->getAttribute(
                        $relationship->getLocalKeyName(),
                    ),
                );

                return;
            }

            /** @var BelongsTo $relationship */
            $relatedModel = $relationship->getResults();

            $component->state(
                $relatedModel?->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->getOptionLabelUsing(static function (ModalTableSelect $component) {
            $record = $component->getSelectedRecord();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (str_contains($relationshipTitleAttribute, '->')) {
                $relationshipTitleAttribute = str_replace('->', '.', $relationshipTitleAttribute);
            }

            return data_get($record, $relationshipTitleAttribute);
        });

        $this->getSelectedRecordUsing(static function (ModalTableSelect $component, $state) use ($modifyQueryUsing): ?Model {
            $relationship = $component->getRelationship();

            if (
                (! $modifyQueryUsing) &&
                ($relationship instanceof BelongsTo)
            ) {
                $record = $component->getRecord();

                if (
                    ($record instanceof Model) &&
                    $record->relationLoaded($component->getRelationshipName())
                ) {
                    $relatedRecord = $record->getRelationValue($component->getRelationshipName());

                    if (
                        ($relatedRecord instanceof Model) &&
                        ((string) $relatedRecord->getAttribute($relationship->getOwnerKeyName()) === (string) $state)
                    ) {
                        return $relatedRecord;
                    }
                }
            }

            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            $relationshipQuery->where($component->getQualifiedRelatedKeyNameForRelationship($relationship), $state);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            return $relationshipQuery->first();
        });

        $this->getOptionLabelsUsing(static function (ModalTableSelect $component, array $values) use ($modifyQueryUsing): array {
            $relationship = $component->getRelationship();
            $record = $component->getRecord();
            $relationshipName = $component->getRelationshipName();

            if (
                (! $modifyQueryUsing) &&
                ($record instanceof Model) &&
                $record->relationLoaded($relationshipName) &&
                (
                    ($relationship instanceof BelongsToMany) ||
                    ($relationship instanceof HasOneOrMany)
                )
            ) {
                $relatedRecords = $record->getRelationValue($relationshipName);

                if ($relatedRecords instanceof Collection) {
                    $relatedKeyName = ($relationship instanceof BelongsToMany)
                        ? $relationship->getRelatedKeyName()
                        : $relationship->getRelated()->getKeyName();

                    $loadedKeys = $relatedRecords->pluck($relatedKeyName)->map(fn ($key) => (string) $key)->all();
                    $requestedKeys = array_map(fn ($value) => (string) $value, $values);

                    if (empty(array_diff($requestedKeys, $loadedKeys))) {
                        $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

                        if (str_contains($relationshipTitleAttribute, '->')) {
                            $relationshipTitleAttribute = str_replace('->', '.', $relationshipTitleAttribute);
                        }

                        $filteredRecords = $relatedRecords->filter(
                            fn (Model $relatedRecord): bool => in_array(
                                (string) $relatedRecord->getAttribute($relatedKeyName),
                                $requestedKeys,
                                strict: true,
                            ),
                        );

                        if ($component->hasOptionLabelFromRecordUsingCallback()) {
                            return $filteredRecords
                                ->mapWithKeys(static fn (Model $relatedRecord) => [
                                    $relatedRecord->getAttribute($relatedKeyName) => $component->getOptionLabelFromRecord($relatedRecord),
                                ])
                                ->toArray();
                        }

                        return $filteredRecords
                            ->pluck($relationshipTitleAttribute, $relatedKeyName)
                            ->toArray();
                    }
                }
            }

            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

            $relationshipQuery->whereIn($qualifiedRelatedKeyName, $values);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (str_contains($relationshipTitleAttribute, '->')) {
                if (! str_contains($relationshipTitleAttribute, ' as ')) {
                    $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                }
            } else {
                $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $qualifiedRelatedKeyName)
                ->toArray();
        });

        $this->saveRelationshipsUsing(static function (ModalTableSelect $component, Model $record, $state) use ($modifyQueryUsing): void {
            $relationship = $component->getRelationship();

            if (($relationship instanceof HasOne) || ($relationship instanceof HasMany)) {
                $query = $relationship->getQuery();

                if ($modifyQueryUsing) {
                    $component->evaluate($modifyQueryUsing, [
                        'query' => $query,
                        'search' => null,
                    ]);
                }

                $query->update([
                    $relationship->getForeignKeyName() => null,
                ]);

                if (! empty($state)) {
                    $relationship::noConstraints(function () use ($component, $record, $state, $modifyQueryUsing): void {
                        $relationship = $component->getRelationship();

                        $query = $relationship->getQuery()->whereIn($relationship->getLocalKeyName(), Arr::wrap($state));

                        if ($modifyQueryUsing) {
                            $component->evaluate($modifyQueryUsing, [
                                'query' => $query,
                                'search' => null,
                            ]);
                        }

                        $query->update([
                            $relationship->getForeignKeyName() => $record->getAttribute($relationship->getLocalKeyName()),
                        ]);
                    });
                }

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
                // If the model is new and the foreign key is already filled, we don't need to fill it again.
                // This could be a security issue if the foreign key was mutated in some way before it
                // was saved, and we don't want to overwrite that value.
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

            if ($modifyQueryUsing) {
                $component->evaluate($modifyQueryUsing, [
                    'query' => $relationship->getQuery(),
                    'search' => null,
                ]);
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

            $pivotData = $component->getPivotData();

            if ($pivotData === []) {
                $relationship->sync($state, detaching: false);

                return;
            }

            $relationship->syncWithPivotValues($state, $pivotData, detaching: false);
        });

        $this->dehydrated(fn (ModalTableSelect $component): bool => (! $component->isMultiple()) && $component->isSaved());

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

    public function getOptionLabelFromRecord(Model $record): string | Htmlable
    {
        return $this->evaluate(
            $this->getOptionLabelFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
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

    public function getSelectedRecord(): ?Model
    {
        if ($this->cachedSelectedRecord) {
            return $this->cachedSelectedRecord;
        }

        if (blank($this->getState())) {
            return null;
        }

        return $this->cachedSelectedRecord = $this->evaluate($this->getSelectedRecordUsing);
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    protected function getQualifiedRelatedKeyNameForRelationship(Relation $relationship): string
    {
        if ($relationship instanceof BelongsToMany) {
            return $relationship->getQualifiedRelatedKeyName();
        }

        if ($relationship instanceof HasOneOrManyThrough) {
            return $relationship->getQualifiedForeignKeyName();
        }

        if (
            ($relationship instanceof HasOneOrMany) ||
            ($relationship instanceof BelongsToThrough)
        ) {
            return $relationship->getRelated()->getQualifiedKeyName();
        }

        /** @var BelongsTo $relationship */

        return $relationship->getQualifiedOwnerKeyName();
    }

    /**
     * @return ?array<string>
     */
    public function getInValidationRuleValues(): ?array
    {
        $values = parent::getInValidationRuleValues();

        if ($values !== null) {
            return $values;
        }

        $state = $this->getState();

        if (blank($state)) {
            return null;
        }

        if ($this->isMultiple()) {
            return array_keys($this->getOptionLabels(withDefaults: false));
        }

        return blank($this->getOptionLabel(withDefault: false)) ? [] : null;
    }

    public function hasInValidationOnMultipleValues(): bool
    {
        return $this->isMultiple();
    }

    public function tableConfiguration(string | Closure $tableConfiguration): static
    {
        $this->tableConfiguration = $tableConfiguration;

        return $this;
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

    public function badge(bool | Closure | null $condition = true): static
    {
        $this->hasBadges = $condition;

        return $this;
    }

    public function hasBadges(): bool
    {
        return $this->evaluate($this->hasBadges) ?? $this->isMultiple();
    }

    public function badgeColor(string | Closure | null $color): static
    {
        $this->badgeColor = $color;

        return $this;
    }

    public function getBadgeColor(): ?string
    {
        return $this->evaluate($this->badgeColor);
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
