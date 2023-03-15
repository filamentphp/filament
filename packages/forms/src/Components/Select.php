<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Livewire\Component as LivewireComponent;

class Select extends Field implements Contracts\HasAffixActions, Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\CanDisableOptions;
    use Concerns\CanSelectPlaceholder;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasLoadingMessage;
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.select';

    /**
     * @var array<Component> | Closure | null
     */
    protected array | Closure | null $createOptionActionForm = null;

    protected ?Closure $createOptionUsing = null;

    protected string | Closure | null $createOptionModalHeading = null;

    protected string | Closure | null $editOptionModalHeading = null;

    protected ?Closure $modifyCreateOptionActionUsing = null;

    protected ?Closure $modifyManageOptionActionsUsing = null;

    /**
     * @var array<Component> | Closure | null
     */
    protected array | Closure | null $editOptionActionForm = null;

    protected ?Closure $fillEditOptionActionFormUsing = null;

    protected ?Closure $updateOptionUsing = null;

    protected ?Closure $modifyEditOptionActionUsing = null;

    protected ?Model $cachedSelectedRecord = null;

    protected bool | Closure $isMultiple = false;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected ?Closure $getSelectedRecordUsing = null;

    /**
     * @var array<string> | null
     */
    protected ?array $searchColumns = null;

    protected string | Closure | null $maxItemsMessage = null;

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected string | Closure | null $position = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected int | Closure $optionsLimit = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(static fn (Select $component): ?array => $component->isMultiple() ? [] : null);

        $this->afterStateHydrated(static function (Select $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->getOptionLabelUsing(static function (Select $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });

        $this->getOptionLabelsUsing(static function (Select $component, array $values): array {
            $options = $component->getOptions();

            $labels = [];

            foreach ($values as $value) {
                $labels[$value] = $options[$value] ?? $value;
            }

            return $labels;
        });

        $this->placeholder(__('filament-forms::components.select.placeholder'));

        $this->suffixActions([
            fn (Select $component): ?Action => $component->getCreateOptionAction(),
            fn (Select $component): ?Action => $component->getEditOptionAction(),
        ]);
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null, ?string $placeholder = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.select.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.select.boolean.false'),
        ]);

        $this->placeholder($placeholder ?? '-');

        return $this;
    }

    public function createOptionAction(?Closure $callback): static
    {
        $this->modifyCreateOptionActionUsing = $callback;

        return $this;
    }

    public function manageOptionActions(?Closure $callback): static
    {
        $this->modifyManageOptionActionsUsing = $callback;

        return $this;
    }

    /**
     * @param  array<Component> | Closure | null  $schema
     */
    public function manageOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionForm($schema);
        $this->editOptionForm($schema);

        return $this;
    }

    /**
     * @param  array<Component> | Closure | null  $schema
     */
    public function createOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionActionForm = $schema;

        return $this;
    }

    public function createOptionUsing(Closure $callback): static
    {
        $this->createOptionUsing = $callback;

        return $this;
    }

    public function getCreateOptionUsing(): ?Closure
    {
        return $this->createOptionUsing;
    }

    public function getCreateOptionActionName(): string
    {
        return 'createOption';
    }

    public function getCreateOptionAction(): ?Action
    {
        if ($this->isDisabled()) {
            return null;
        }

        if (! $this->hasCreateOptionActionFormSchema()) {
            return null;
        }

        $action = Action::make($this->getCreateOptionActionName())
            ->form(function (Select $component, Form $form): array | Form | null {
                return $component->getCreateOptionActionForm($form->model(
                    $component->getRelationship() ? $component->getRelationship()->getModel()::class : null,
                ));
            })
            ->action(static function (Action $action, array $arguments, Select $component, array $data, ComponentContainer $form) {
                if (! $component->getCreateOptionUsing()) {
                    throw new Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
                }

                $createdOptionKey = $component->evaluate($component->getCreateOptionUsing(), [
                    'data' => $data,
                    'form' => $form,
                ]);

                $state = $component->isMultiple() ?
                    array_merge($component->getState(), [$createdOptionKey]) :
                    $createdOptionKey;

                $component->state($state);
                $component->callAfterStateUpdated();

                if (! ($arguments['another'] ?? false)) {
                    return;
                }

                $action->callAfter();

                $form->fill();

                $action->halt();
            })
            ->icon('heroicon-m-plus')
            ->iconButton()
            ->modalHeading($this->getCreateOptionModalHeading() ?? __('filament-forms::components.select.actions.create_option.modal.heading'))
            ->modalButton(__('filament-forms::components.select.actions.create_option.modal.actions.create.label'))
            ->extraModalActions(fn (Action $action, Select $component): array => $component->isMultiple() ? [
                $action->makeExtraModalAction('createAnother', ['another' => true])
                    ->label(__('filament-forms::components.select.actions.create_option.modal.actions.create_another.label')),
            ] : []);

        if ($this->modifyManageOptionActionsUsing) {
            $action = $this->evaluate($this->modifyManageOptionActionsUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($this->modifyCreateOptionActionUsing) {
            $action = $this->evaluate($this->modifyCreateOptionActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function createOptionModalHeading(string | Closure | null $heading): static
    {
        $this->createOptionModalHeading = $heading;

        return $this;
    }

    public function editOptionModalHeading(string | Closure | null $heading): static
    {
        $this->editOptionModalHeading = $heading;

        return $this;
    }

    public function editOptionAction(?Closure $callback): static
    {
        $this->modifyEditOptionActionUsing = $callback;

        return $this;
    }

    /**
     * @return array<Component> | Form | null
     */
    public function getCreateOptionActionForm(Form $form): array | Form | null
    {
        return $this->evaluate($this->createOptionActionForm, ['form' => $form]);
    }

    public function hasCreateOptionActionFormSchema(): bool
    {
        return (bool) $this->createOptionActionForm;
    }

    /**
     * @return array<Component> | Form | null
     */
    public function getEditOptionActionForm(Form $form): array | Form | null
    {
        return $this->evaluate($this->editOptionActionForm, ['form' => $form]);
    }

    public function hasEditOptionActionFormSchema(): bool
    {
        return (bool) $this->editOptionActionForm;
    }

    /**
     * @param  array<Component> | Closure | null  $schema
     */
    public function editOptionForm(array | Closure | null $schema): static
    {
        $this->editOptionActionForm = $schema;
        $this->reactive();

        return $this;
    }

    public function updateOptionUsing(Closure $callback): static
    {
        $this->updateOptionUsing = $callback;

        return $this;
    }

    public function getUpdateOptionUsing(): ?Closure
    {
        return $this->updateOptionUsing;
    }

    public function getEditOptionActionName(): string
    {
        return 'editOption';
    }

    public function getEditOptionAction(): ?Action
    {
        if ($this->isDisabled()) {
            return null;
        }

        if ($this->isMultiple()) {
            return null;
        }

        if (blank($this->getState())) {
            return null;
        }

        if (! $this->hasEditOptionActionFormSchema()) {
            return null;
        }

        $action = Action::make($this->getEditOptionActionName())
            ->form(function (Select $component, Form $form): array | Form | null {
                return $component->getEditOptionActionForm(
                    $form->model($component->getSelectedRecord()),
                );
            })
            ->fillForm($this->getEditOptionActionFormData())
            ->action(static function (Action $action, array $arguments, Select $component, array $data, ComponentContainer $form) {
                $statePath = $component->getStatePath();

                if (! $component->getUpdateOptionUsing()) {
                    throw new Exception("Select field [{$statePath}] must have a [updateOptionUsing()] closure set.");
                }

                $component->evaluate($component->getUpdateOptionUsing(), [
                    'data' => $data,
                    'form' => $form,
                ]);

                /** @var LivewireComponent $livewire */
                $livewire = $component->getLivewire();
                $livewire->dispatchBrowserEvent('filament-forms::select/refreshSelectedOptionLabel', [
                    'livewireId' => $livewire->id,
                    'statePath' => $statePath,
                ]);
            })
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading($this->getEditOptionModalHeading() ?? __('filament-forms::components.select.actions.edit_option.modal.heading'))
            ->modalButton(__('filament-forms::components.select.actions.edit_option.modal.actions.save.label'));

        if ($this->modifyManageOptionActionsUsing) {
            $action = $this->evaluate($this->modifyManageOptionActionsUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($this->modifyEditOptionActionUsing) {
            $action = $this->evaluate($this->modifyEditOptionActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    /**
     * @return array<string, mixed>
     */
    public function getEditOptionActionFormData(): array
    {
        return $this->evaluate($this->fillEditOptionActionFormUsing);
    }

    public function fillEditOptionActionFormUsing(?Closure $callback): static
    {
        $this->fillEditOptionActionFormUsing = $callback;

        return $this;
    }

    public function getCreateOptionModalHeading(): ?string
    {
        return $this->evaluate($this->createOptionModalHeading);
    }

    public function getEditOptionModalHeading(): ?string
    {
        return $this->evaluate($this->editOptionModalHeading);
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

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    /**
     * @param  bool | array<string> | Closure  $condition
     */
    public function searchable(bool | array | Closure $condition = true): static
    {
        if (is_array($condition)) {
            $this->isSearchable = true;
            $this->searchColumns = $condition;
        } else {
            $this->isSearchable = $condition;
            $this->searchColumns = null;
        }

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function position(string | Closure | null $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function maxItemsMessage(string | Closure | null $message): static
    {
        $this->maxItemsMessage = $message;

        return $this;
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->evaluate($this->position);
    }

    public function getOptionLabel(): ?string
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => $this->getState(),
        ]);
    }

    /**
     * @return array<string>
     */
    public function getOptionLabels(): array
    {
        $labels = $this->evaluate($this->getOptionLabelsUsing, [
            'values' => $this->getState(),
        ]);

        if ($labels instanceof Arrayable) {
            $labels = $labels->toArray();
        }

        return $labels;
    }

    /**
     * @return array<string>
     */
    public function getSearchColumns(): ?array
    {
        $columns = $this->searchColumns;

        if ($this->hasRelationship()) {
            $columns ??= [$this->getRelationshipTitleAttribute()];
        }

        return $columns;
    }

    /**
     * @return array<string>
     */
    public function getSearchResults(string $search): array
    {
        if (! $this->getSearchResultsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getSearchResultsUsing, [
            'query' => $search,
            'search' => $search,
            'searchQuery' => $search,
        ]);

        if ($results instanceof Arrayable) {
            $results = $results->toArray();
        }

        return $results;
    }

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getSearchResultsForJs(string $search): array
    {
        return $this->transformOptionsForJs($this->getSearchResults($search));
    }

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getOptionsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptions());
    }

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getOptionLabelsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptionLabels());
    }

    /**
     * @param  array<string>  $options
     * @return array<array{'label': string, 'value': string}>
     */
    protected function transformOptionsForJs(array $options): array
    {
        return collect($options)
            ->map(fn ($label, $value): array => ['label' => $label, 'value' => strval($value)])
            ->values()
            ->all();
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->isSearchable) || $this->isMultiple();
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleAttribute, ?Closure $callback = null): static
    {
        $this->relationship = $relationshipName;
        $this->relationshipTitleAttribute = $titleAttribute;

        $this->getSearchResultsUsing(static function (Select $component, ?string $search) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query();

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($component->getRelationshipTitleAttribute());
            }

            $component->applySearchConstraint(
                $relationshipQuery,
                strtolower($search),
            );

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $component->optionsLimit($baseRelationshipQuery->limit);
            } else {
                $relationshipQuery->limit($component->getOptionsLimit());
            }

            if ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                $keyName = $relationship->getRelated()->getKeyName();
            } else {
                $keyName = $component->isMultiple() ? $relationship->getRelatedKeyName() : $relationship->getOwnerKeyName();
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$keyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (
                str_contains($relationshipTitleAttribute, '->') &&
                (! str_contains($relationshipTitleAttribute, ' as '))
            ) {
                $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $keyName)
                ->toArray();
        });

        $this->options(static function (Select $component) use ($callback): ?array {
            if (($component->isSearchable()) && ! $component->isPreloaded()) {
                return null;
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query();

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($component->getRelationshipTitleAttribute());
            }

            if ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                $keyName = $relationship->getRelated()->getKeyName();
            } else {
                $keyName = $component->isMultiple() ? $relationship->getRelatedKeyName() : $relationship->getOwnerKeyName();
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$keyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (
                str_contains($relationshipTitleAttribute, '->') &&
                (! str_contains($relationshipTitleAttribute, ' as '))
            ) {
                $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $keyName)
                ->toArray();
        });

        $this->loadStateFromRelationshipsUsing(static function (Select $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($component->isMultiple()) {
                $relatedModels = $relationship->getResults();

                $component->state(
                    // Cast the related keys to a string, otherwise JavaScript does not
                    // know how to handle deselection.
                    //
                    // https://github.com/filamentphp/filament/issues/1111
                    $relatedModels
                        ->pluck($relationship->getRelatedKeyName())
                        ->map(static fn ($key): string => strval($key))
                        ->toArray(),
                );

                return;
            }

            /** @var BelongsTo $relationship */
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

        $this->getOptionLabelUsing(static function (Select $component) {
            $record = $component->getSelectedRecord();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            return $record->getAttributeValue($component->getRelationshipTitleAttribute());
        });

        $this->getSelectedRecordUsing(static function (Select $component, $state) use ($callback): ?Model {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->where($relationship->getOwnerKeyName(), $state);

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            return $relationshipQuery->first();
        });

        $this->getOptionLabelsUsing(static function (Select $component, array $values) use ($callback): array {
            $relationship = $component->getRelationship();
            $relatedKeyName = $relationship->getRelatedKeyName();

            $relationshipQuery = $relationship->getRelated()->query()
                ->whereIn($relatedKeyName, $values);

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relatedKeyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (
                str_contains($relationshipTitleAttribute, '->') &&
                (! str_contains($relationshipTitleAttribute, ' as '))
            ) {
                $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $relatedKeyName)
                ->toArray();
        });

        $this->rule(
            static function (Select $component): Exists {
                if ($component->getRelationship() instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                    $column = $component->getRelationship()->getRelated()->getKeyName();
                } else {
                    $column = $component->getRelationship()->getOwnerKeyName();
                }

                return Rule::exists(
                    $component->getRelationship()->getModel()::class,
                    $column,
                );
            },
            static fn (Select $component): bool => ! $component->isMultiple(),
        );

        $this->saveRelationshipsUsing(static function (Select $component, Model $record, $state) {
            if ($component->isMultiple()) {
                $component->getRelationship()->sync($state ?? []);

                return;
            }

            $component->getRelationship()->associate($state);
            $record->save();
        });

        $this->createOptionUsing(static function (Select $component, array $data, Form $form) {
            $record = $component->getRelationship()->getRelated();
            $record->fill($data);
            $record->save();

            $form->model($record)->saveRelationships();

            return $record->getKey();
        });

        $this->fillEditOptionActionFormUsing(static function (Select $component): ?array {
            return $component->getSelectedRecord()?->attributesToArray();
        });

        $this->updateOptionUsing(static function (array $data, Form $form) {
            $form->getRecord()?->update($data);
        });

        $this->dehydrated(fn (Select $component): bool => ! $component->isMultiple());

        return $this;
    }

    protected function applySearchConstraint(Builder $query, string $search): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $isFirst = true;

        $query->where(function (Builder $query) use ($isFirst, $searchOperator, $search): Builder {
            foreach ($this->getSearchColumns() as $searchColumn) {
                $whereClause = $isFirst ? 'where' : 'orWhere';

                $query->{$whereClause}(
                    $searchColumn,
                    $searchOperator,
                    "%{$search}%",
                );

                $isFirst = false;
            }

            return $query;
        });

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

    public function getRelationshipTitleAttribute(): string
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

    public function getRelationship(): BelongsTo | BelongsToMany | \Znck\Eloquent\Relations\BelongsToThrough | null
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
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

    public function hasDynamicOptions(): bool
    {
        if ($this->hasRelationship()) {
            return $this->isPreloaded();
        }

        return $this->options instanceof Closure;
    }

    public function hasDynamicSearchResults(): bool
    {
        if ($this->hasRelationship()) {
            return ! $this->isPreloaded();
        }

        return $this->getSearchResultsUsing instanceof Closure;
    }

    public function getActionFormModel(): Model | string | null
    {
        if ($this->hasRelationship()) {
            return $this->getRelationship()->getModel()::class;
        }

        return parent::getActionFormModel();
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function getMaxItemsMessage(): string
    {
        $maxItems = $this->getMaxItems();

        return $this->evaluate($this->maxItemsMessage) ?? trans_choice('filament-forms::components.select.max_items_message', $maxItems, [
            ':count' => $maxItems,
        ]);
    }
}
