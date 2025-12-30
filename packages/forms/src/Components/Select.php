<?php

namespace Filament\Forms\Components;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumArrayStateCast;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
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
use Livewire\Attributes\Renderless;
use LogicException;
use Znck\Eloquent\Relations\BelongsToThrough;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

class Select extends Field implements Contracts\CanDisableOptions, Contracts\HasNestedRecursiveValidationRules, HasAffixActions
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBeNative;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\CanLimitItemsLength;
    use Concerns\CanSelectPlaceholder;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasLoadingMessage;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasOptions;
    use Concerns\HasPivotData;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.select';

    /**
     * @var array<Component | Action | ActionGroup> | Closure | null
     */
    protected array | Closure | null $createOptionActionForm = null;

    protected ?Closure $createOptionUsing = null;

    protected string | Closure | null $createOptionModalHeading = null;

    protected string | Closure | null $editOptionModalHeading = null;

    protected ?Closure $modifyCreateOptionActionUsing = null;

    protected ?Closure $modifyManageOptionActionsUsing = null;

    /**
     * @var array<Component | Action | ActionGroup> | Closure | null
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

    protected ?Closure $transformOptionsForJsUsing = null;

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

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    protected bool | Closure $canOptionLabelsWrap = true;

    protected bool | Closure $isReorderable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformOptionsForJsUsing(static function (Select $component, array $options): array {
            return collect($options)
                ->map(fn ($label, $value): array => is_array($label)
                    ? ['label' => $value, 'options' => $component->transformOptionsForJs($label)]
                    : ['label' => $label, 'value' => strval($value), 'isDisabled' => $component->isOptionDisabled($value, $label)])
                ->values()
                ->all();
        });

        $this->placeholder(static fn (Select $component): ?string => $component->isDisabled() ? null : __('filament-forms::components.select.placeholder'));

        $this->suffixActions([
            static fn (Select $component): ?Action => $component->getCreateOptionAction(),
            static fn (Select $component): ?Action => $component->getEditOptionAction(),
        ]);
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null, ?string $placeholder = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.select.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.select.boolean.false'),
        ]);

        $this->placeholder($placeholder ?? '-');

        $this->stateCast(app(BooleanStateCast::class, ['isStoredAsInt' => true]));

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
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function manageOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionForm($schema);
        $this->editOptionForm($schema);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function createOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionActionForm = $schema;

        return $this;
    }

    public function createOptionUsing(?Closure $callback): static
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
            ->label(__('filament-forms::components.select.actions.create_option.label'))
            ->schema(static function (Select $component, Schema $schema): array | Schema | null {
                return $component->getCreateOptionActionForm($schema->model(
                    $component->getRelationship() ? $component->getRelationship()->getModel()::class : null,
                ));
            })
            ->action(static function (Action $action, array $arguments, Select $component, array $data, Schema $schema): void {
                if (! $component->getCreateOptionUsing()) {
                    throw new LogicException("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
                }

                $createdOptionKey = $component->evaluate($component->getCreateOptionUsing(), [
                    'data' => $data,
                    'form' => $schema,
                    'schema' => $schema,
                ]);

                $state = $component->isMultiple()
                    ? [
                        ...$component->getState(),
                        $createdOptionKey,
                    ]
                    : $createdOptionKey;

                $component->state($state);
                $component->callAfterStateUpdated();

                if (! ($arguments['another'] ?? false)) {
                    return;
                }

                $action->callAfter();

                $schema->fill();

                $action->halt();
            })
            ->color('gray')
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_SELECT_ACTIONS_CREATE_OPTION) ?? Heroicon::Plus)
            ->iconButton()
            ->modalHeading($this->getCreateOptionModalHeading() ?? __('filament-forms::components.select.actions.create_option.modal.heading'))
            ->modalSubmitActionLabel(__('filament-forms::components.select.actions.create_option.modal.actions.create.label'))
            ->extraModalFooterActions(fn (Action $action, Select $component): array => $component->isMultiple() ? [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
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
     * @return array<Component | Action | ActionGroup> | Schema | null
     */
    public function getCreateOptionActionForm(Schema $schema): array | Schema | null
    {
        return $this->evaluate($this->createOptionActionForm, ['form' => $schema, 'schema' => $schema]);
    }

    public function hasCreateOptionActionFormSchema(): bool
    {
        return (bool) $this->createOptionActionForm;
    }

    /**
     * @return array<Component | Action | ActionGroup> | Schema | null
     */
    public function getEditOptionActionForm(Schema $schema): array | Schema | null
    {
        return $this->evaluate($this->editOptionActionForm, ['form' => $schema, 'schema' => $schema]);
    }

    public function hasEditOptionActionFormSchema(): bool
    {
        return (bool) $this->editOptionActionForm;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Closure | null  $schema
     */
    public function editOptionForm(array | Closure | null $schema): static
    {
        $this->editOptionActionForm = $schema;
        $this->live();

        return $this;
    }

    public function updateOptionUsing(?Closure $callback): static
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

        if (! $this->hasEditOptionActionFormSchema()) {
            return null;
        }

        $action = Action::make($this->getEditOptionActionName())
            ->label(__('filament-forms::components.select.actions.edit_option.label'))
            ->schema(static function (Select $component, Schema $schema): array | Schema | null {
                return $component->getEditOptionActionForm(
                    $schema->model($component->getSelectedRecord()),
                );
            })
            ->fillForm(static fn (Select $component): ?array => $component->getEditOptionActionFormData())
            ->action(static function (Action $action, array $arguments, Select $component, array $data, Schema $schema): void {
                if (! $component->getUpdateOptionUsing()) {
                    throw new LogicException("Select field [{$component->getStatePath()}] must have a [updateOptionUsing()] closure set.");
                }

                $component->evaluate($component->getUpdateOptionUsing(), [
                    'data' => $data,
                    'form' => $schema,
                    'schema' => $schema,
                ]);

                $component->refreshSelectedOptionLabel();
            })
            ->color('gray')
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_SELECT_ACTIONS_EDIT_OPTION) ?? Heroicon::PencilSquare)
            ->iconButton()
            ->modalHeading($this->getEditOptionModalHeading() ?? __('filament-forms::components.select.actions.edit_option.modal.heading'))
            ->modalSubmitActionLabel(__('filament-forms::components.select.actions.edit_option.modal.actions.save.label'))
            ->visible(fn (): bool => filled($this->getState()));

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
     * @return ?array<string, mixed>
     */
    public function getEditOptionActionFormData(): ?array
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

    public function transformOptionsForJsUsing(?Closure $callback): static
    {
        $this->transformOptionsForJsUsing = $callback;

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

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
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

    #[ExposedLivewireMethod]
    #[Renderless]
    public function getOptionLabel(bool $withDefault = true): ?string
    {
        if (! $this->getOptionLabelUsing) {
            $state = $this->getState();
            $options = $this->getOptions();

            if ($state instanceof BackedEnum) {
                $state = $state->value;
            }

            foreach ($options as $groupedOptions) {
                if (! is_array($groupedOptions)) {
                    continue;
                }

                if (blank($groupedOptions[$state] ?? null)) {
                    continue;
                }

                return $groupedOptions[$state];
            }

            if (filled($options[$state] ?? null) && (! is_array($options[$state]))) {
                return $options[$state];
            }

            if ($withDefault) {
                return $state;
            }

            return null;
        }

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
     * @return array<string>
     */
    public function getOptionLabels(bool $withDefaults = true): array
    {
        if (! $this->getOptionLabelsUsing) {
            $state = $this->getState();
            $options = $this->getOptions();

            $labels = [];

            foreach ($state as $value) {
                if ($value instanceof BackedEnum) {
                    $value = $value->value;
                }

                foreach ($options as $groupedOptions) {
                    if (! is_array($groupedOptions)) {
                        continue;
                    }

                    if (blank($groupedOptions[$value] ?? null)) {
                        continue;
                    }

                    $labels[$value] = $groupedOptions[$value];

                    continue 2;
                }

                if (
                    filled($options[$value] ?? null)
                    && (! is_array($options[$value]))
                ) {
                    $labels[$value] = $options[$value];

                    continue;
                }

                if ($withDefaults) {
                    $labels[$value] = $value;
                }
            }

            return $labels;
        }

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

    /**
     * @return array<string>
     */
    public function getSearchColumns(): ?array
    {
        $columns = $this->searchColumns;

        if ($this->hasRelationship() && (filled($relationshipTitleAttribute = $this->getRelationshipTitleAttribute()))) {
            $columns ??= [$relationshipTitleAttribute];
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
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getSearchResultsForJs(string $search): array
    {
        return $this->transformOptionsForJs($this->getSearchResults($search));
    }

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getOptionsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptions());
    }

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getOptionLabelsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptionLabels());
    }

    /**
     * @param  array<string | array<string>>  $options
     * @return array<array<string, mixed>>
     */
    protected function transformOptionsForJs(array $options): array
    {
        if (empty($options)) {
            return [];
        }

        $transformedOptions = $this->evaluate($this->transformOptionsForJsUsing, [
            'options' => $options,
        ]);

        if ($transformedOptions instanceof Arrayable) {
            return $transformedOptions->toArray();
        }

        return $transformedOptions;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->isSearchable) ?? $this->isMultiple();
    }

    public function relationship(string | Closure | null $name = null, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null, bool $ignoreRecord = false): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;

        $this->getSearchResultsUsing(static function (Select $component, ?string $search) use ($modifyQueryUsing, $ignoreRecord): array {
            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($ignoreRecord && ($record = $component->getRecord())) {
                $relationshipQuery->where($record->getQualifiedKeyName(), '!=', $record->getKey());
            }

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => $search,
                ]) ?? $relationshipQuery;
            }

            $component->applySearchConstraint(
                $relationshipQuery,
                generate_search_term_expression($search, $component->isSearchForcedCaseInsensitive(), $relationshipQuery->getConnection()),
            );

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $component->optionsLimit($baseRelationshipQuery->limit);
            } else {
                $relationshipQuery->limit($component->getOptionsLimit());
            }

            $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipOrderByAttribute = $relationshipTitleAttribute;

                if (str_contains($relationshipOrderByAttribute, ' as ')) {
                    $relationshipOrderByAttribute = (string) str($relationshipOrderByAttribute)->before(' as ');
                }

                $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipOrderByAttribute));
            }

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

        $this->options(static function (Select $component) use ($modifyQueryUsing, $ignoreRecord): ?array {
            if (($component->isSearchable()) && ! $component->isPreloaded()) {
                return null;
            }

            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($ignoreRecord && ($record = $component->getRecord())) {
                $relationshipQuery->where($record->getQualifiedKeyName(), '!=', $record->getKey());
            }

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $component->optionsLimit($baseRelationshipQuery->limit);
            } elseif ($component->isSearchable() && filled($component->getSearchColumns())) {
                $relationshipQuery->limit($component->getOptionsLimit());
            }

            $qualifiedRelatedKeyName = $component->getQualifiedRelatedKeyNameForRelationship($relationship);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipOrderByAttribute = $relationshipTitleAttribute;

                if (str_contains($relationshipOrderByAttribute, ' as ')) {
                    $relationshipOrderByAttribute = (string) str($relationshipOrderByAttribute)->before(' as ');
                }

                $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipOrderByAttribute));
            }

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

        $this->loadStateFromRelationshipsUsing(static function (Select $component, $state) use ($modifyQueryUsing): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if (
                ($relationship instanceof BelongsToMany) ||
                ($relationship instanceof HasOneOrManyThrough)
            ) {
                if ($modifyQueryUsing) {
                    $component->evaluate($modifyQueryUsing, [
                        'query' => $relationship->getQuery(),
                        'search' => null,
                    ]);
                }

                /** @var Collection $relatedRecords */
                $relatedRecords = $relationship->getResults();

                // Cast the related keys to a string, otherwise JavaScript does not
                // know how to handle deselection.
                //
                // https://github.com/filamentphp/filament/issues/1111
                $relatedKeys = $relatedRecords
                    ->pluck(($relationship instanceof BelongsToMany) ? $relationship->getRelatedKeyName() : $relationship->getRelated()->getKeyName())
                    ->map(static fn ($key): string => strval($key));

                $component->state(
                    $component->isMultiple()
                        ? $relatedKeys->all()
                        : $relatedKeys->first(),
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
                    $relatedRecords
                        ->pluck($relationship->getLocalKeyName())
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

        $this->getOptionLabelUsing(static function (Select $component) {
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

        $this->getSelectedRecordUsing(static function (Select $component, $state) use ($modifyQueryUsing): ?Model {
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

        $this->getOptionLabelsUsing(static function (Select $component, array $values) use ($modifyQueryUsing): array {
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

        $this->saveRelationshipsUsing(static function (Select $component, Model $record, $state) use ($modifyQueryUsing): void {
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

        $this->createOptionUsing(static function (Select $component, array $data, Schema $schema) {
            $record = $component->getRelationship()->newModelInstance();
            $record->fill($data);
            $record->save();

            $schema->model($record)->saveRelationships();

            return $record->getKey();
        });

        $this->fillEditOptionActionFormUsing(static function (Select $component): ?array {
            return $component->getSelectedRecord()?->attributesToArray();
        });

        $this->updateOptionUsing(static function (array $data, Schema $schema): void {
            $schema->getRecord()?->update($data);
        });

        $this->dehydrated(fn (Select $component): bool => (! $component->isMultiple()) && $component->isSaved());

        return $this;
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function applySearchConstraint(Builder $query, string $search): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $isForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive();

        $query->where(function (Builder $query) use ($databaseConnection, $isForcedCaseInsensitive, $search): Builder {
            $isFirst = true;

            foreach ($this->getSearchColumns() ?? [] as $searchColumn) {
                $whereClause = $isFirst ? 'where' : 'orWhere';

                $query->{$whereClause}(
                    generate_search_column_expression($searchColumn, $isForcedCaseInsensitive, $databaseConnection),
                    'like',
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

    public function hasDynamicOptions(): bool
    {
        if ($this->hasDynamicDisabledOptions()) {
            return true;
        }

        if ($this->hasRelationship()) {
            return $this->isPreloaded();
        }

        return $this->options instanceof Closure;
    }

    public function hasDynamicSearchResults(): bool
    {
        if ($this->hasRelationship() && blank($this->getSearchColumns())) {
            return false;
        }

        return $this->getSearchResultsUsing instanceof Closure;
    }

    public function hasInitialNoOptionsMessage(): bool
    {
        if ($this->hasRelationship()) {
            return $this->isPreloaded();
        }

        return ! $this->hasDynamicSearchResults();
    }

    /**
     * @return Model | array<string, mixed> | class-string<Model> | null
     */
    public function getActionSchemaModel(): Model | array | string | null
    {
        if ($this->hasRelationship()) {
            return $this->getRelationship()->getModel()::class;
        }

        return parent::getActionSchemaModel();
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

    public function forceSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isSearchForcedCaseInsensitive(): ?bool
    {
        return $this->evaluate($this->isSearchForcedCaseInsensitive);
    }

    public function wrapOptionLabels(bool | Closure $condition = true): static
    {
        $this->canOptionLabelsWrap = $condition;

        return $this;
    }

    public function canOptionLabelsWrap(): bool
    {
        return (bool) $this->evaluate($this->canOptionLabelsWrap);
    }

    public function getQualifiedRelatedKeyNameForRelationship(Relation $relationship): string
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

    public function refreshSelectedOptionLabel(): void
    {
        $livewire = $this->getLivewire();

        $livewire->dispatch(
            'filament-forms::select.refreshSelectedOptionLabel',
            livewireId: $livewire->getId(),
            statePath: $this->getStatePath(),
        );
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            $this->isMultiple() ? EnumArrayStateCast::class : EnumStateCast::class,
            ['enum' => $enum],
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts() || filled($this->getEnum())) {
            return parent::getDefaultStateCasts();
        }

        if ($this->isMultiple()) {
            return [app(OptionsArrayStateCast::class)];
        }

        return [app(OptionStateCast::class, ['isNullable' => true])];
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

        if ($this->isMultiple()) {
            if ((! $this->getOptionLabelsUsing) && ($this->options === null)) {
                throw new LogicException("Filament failed to validate the [{$this->getStatePath()}] field\'s selected options because it did not have an [options()] or [getOptionLabelsUsing()] configuration. Please use one of these methods to inform Filament which options are valid for this field.");
            }

            $state = $this->getState();

            if (blank($state)) {
                return null;
            }

            $optionLabels = $this->getOptionLabels(withDefaults: false);

            if (count($state) > count($optionLabels)) {
                return [];
            }

            $state = array_map(
                static fn (mixed $value): mixed => ($value instanceof BackedEnum) ? $value->value : $value,
                $state,
            );

            if (count(array_diff($state, array_keys($optionLabels)))) {
                return [];
            }

            if ($this->hasDisabledOptions()) {
                foreach ($optionLabels as $optionValue => $optionLabel) {
                    if ($this->isOptionDisabled($optionValue, $optionLabel)) {
                        return [];
                    }
                }
            }

            return null;
        }

        if ((! $this->getOptionLabelUsing) && ($this->options === null)) {
            throw new LogicException("Filament failed to validate the [{$this->getStatePath()}] field\'s selected options because it did not have an [options()] or [getOptionLabelUsing()] configuration. Please use one of these methods to inform Filament which options are valid for this field.");
        }

        $state = $this->getState();

        if (blank($state)) {
            return null;
        }

        $optionLabel = $this->getOptionLabel(withDefault: false);

        if (blank($optionLabel)) {
            return [];
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        if ($this->hasDisabledOptions() && $this->isOptionDisabled($state, $optionLabel)) {
            return [];
        }

        return null;
    }

    public function hasInValidationOnMultipleValues(): bool
    {
        return $this->isMultiple();
    }

    public function hasNullableBooleanState(): bool
    {
        return true;
    }
}
