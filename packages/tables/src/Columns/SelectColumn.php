<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanSelectPlaceholder;
use Filament\Forms\Components\Concerns\HasEnum;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Services\RelationshipJoiner;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Renderless;
use LogicException;
use Stringable;
use Znck\Eloquent\Relations\BelongsToThrough;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

class SelectColumn extends Column implements Editable, HasEmbeddedView
{
    use CanDisableOptions;
    use CanSelectPlaceholder;
    use Concerns\CanBeValidated {
        getRules as getBaseRules;
    }
    use Concerns\CanUpdateState;
    use HasEnum;
    use HasExtraInputAttributes;
    use HasOptions {
        getOptions as getBaseOptions;
    }

    protected bool | Closure $isNative = true;

    protected bool | Closure $areOptionsPreloaded = false;

    protected bool | Closure $areOptionsSearchable = false;

    protected string | Htmlable | Closure | null $noOptionsMessage = null;

    protected string | Htmlable | Closure | null $noOptionsSearchResultsMessage = null;

    protected int | Closure $optionsSearchDebounce = 1000;

    protected string | Closure | null $optionsSearchingMessage = null;

    protected string | Htmlable | Closure | null $optionsSearchPrompt = null;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionsSearchResultsUsing = null;

    protected bool | Closure $shouldSearchOptionLabels = true;

    protected bool | Closure $shouldSearchOptionValues = false;

    protected ?Closure $transformOptionsForJsUsing = null;

    protected string | Closure | null $optionsLoadingMessage = null;

    protected bool | Closure $canOptionLabelsWrap = true;

    protected bool | Closure $isOptionsHtmlAllowed = false;

    protected int | Closure $optionsLimit = 50;

    /**
     * @var array<string> | null
     */
    protected ?array $optionsSearchColumns = null;

    protected string | Closure | null $position = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $optionsRelationship = null;

    protected string | Closure | null $optionsRelationshipTitleAttribute = null;

    protected ?Closure $modifyOptionsRelationshipQueryUsing = null;

    protected bool | Closure | null $isOptionsSearchForcedCaseInsensitive = null;

    protected bool | Closure | null $areOptionsRemembered = null;

    /**
     * @var ?array<string | array<string>>
     */
    protected ?array $rememberedOptions = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->placeholder(__('filament-tables::table.columns.select.placeholder'));

        $this->transformOptionsForJsUsing(static function (SelectColumn $column, array $options): array {
            return collect($options)
                ->map(fn ($label, $value): array => is_array($label)
                    ? ['label' => $value, 'options' => $column->transformOptionsForJs($label)]
                    : ['label' => $label, 'value' => strval($value), 'isDisabled' => $column->isOptionDisabled($value, $label)])
                ->values()
                ->all();
        });
    }

    public function optionsLoadingMessage(string | Closure | null $message): static
    {
        $this->optionsLoadingMessage = $message;

        return $this;
    }

    public function getOptionsLoadingMessage(): string
    {
        return $this->evaluate($this->optionsLoadingMessage) ?? __('filament-forms::components.select.loading_message');
    }

    /**
     * @return array<array-key>
     */
    public function getRules(): array
    {
        $state = $this->getState();

        if (blank($state)) {
            return $this->getBaseRules();
        }

        $optionLabel = $this->getOptionLabel(withDefault: false);

        if (blank($optionLabel)) {
            return [
                ...$this->getBaseRules(),
                Rule::in([]),
            ];
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        if ($this->hasDisabledOptions() && $this->isOptionDisabled($state, $optionLabel)) {
            return [
                ...$this->getBaseRules(),
                Rule::in([]),
            ];
        }

        return $this->getBaseRules();
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionsSearchResultsUsing(?Closure $callback): static
    {
        $this->getOptionsSearchResultsUsing = $callback;

        return $this;
    }

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    /**
     * @param  bool | array<string> | Closure  $condition
     */
    public function searchableOptions(bool | array | Closure $condition = true): static
    {
        if (is_array($condition)) {
            $this->areOptionsSearchable = true;
            $this->optionsSearchColumns = $condition;
        } else {
            $this->areOptionsSearchable = $condition;
            $this->optionsSearchColumns = null;
        }

        return $this;
    }

    public function areOptionsSearchable(): bool
    {
        return (bool) $this->evaluate($this->areOptionsSearchable);
    }

    public function noOptionsMessage(string | Htmlable | Closure | null $message): static
    {
        $this->noOptionsMessage = $message;

        return $this;
    }

    public function noOptionsSearchResultsMessage(string | Htmlable | Closure | null $message): static
    {
        $this->noOptionsSearchResultsMessage = $message;

        return $this;
    }

    public function optionsSearchDebounce(int | Closure $debounce): static
    {
        $this->optionsSearchDebounce = $debounce;

        return $this;
    }

    public function optionsSearchingMessage(string | Closure | null $message): static
    {
        $this->optionsSearchingMessage = $message;

        return $this;
    }

    public function optionsSearchPrompt(string | Htmlable | Closure | null $message): static
    {
        $this->optionsSearchPrompt = $message;

        return $this;
    }

    public function searchOptionLabels(bool | Closure | null $condition = true): static
    {
        $this->shouldSearchOptionLabels = $condition;

        return $this;
    }

    public function searchOptionValues(bool | Closure | null $condition = true): static
    {
        $this->shouldSearchOptionValues = $condition;

        return $this;
    }

    public function getNoOptionsMessage(): string | Htmlable
    {
        return $this->evaluate($this->noOptionsMessage) ?? __('filament-tables::table.columns.select.no_options_message');
    }

    public function getNoOptionsSearchResultsMessage(): string | Htmlable
    {
        return $this->evaluate($this->noOptionsSearchResultsMessage) ?? __('filament-tables::table.columns.select.no_search_results_message');
    }

    public function getOptionsSearchPrompt(): string | Htmlable
    {
        return $this->evaluate($this->optionsSearchPrompt) ?? __('filament-tables::table.columns.select.search_prompt');
    }

    public function shouldSearchOptionLabels(): bool
    {
        return (bool) $this->evaluate($this->shouldSearchOptionLabels);
    }

    public function shouldSearchOptionValues(): bool
    {
        return (bool) $this->evaluate($this->shouldSearchOptionValues);
    }

    /**
     * @return array<string>
     */
    public function getSearchableOptionFields(): array
    {
        return [
            ...($this->shouldSearchOptionLabels() ? ['label'] : []),
            ...($this->shouldSearchOptionValues() ? ['value'] : []),
        ];
    }

    public function getOptionsSearchDebounce(): int
    {
        return $this->evaluate($this->optionsSearchDebounce);
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

    public function getOptionsSearchingMessage(): string
    {
        return $this->evaluate($this->optionsSearchingMessage) ?? __('filament-tables::table.columns.select.searching_message');
    }

    public function allowOptionsHtml(bool | Closure $condition = true): static
    {
        $this->isOptionsHtmlAllowed = $condition;

        return $this;
    }

    public function isOptionsHtmlAllowed(): bool
    {
        return (bool) $this->evaluate($this->isOptionsHtmlAllowed);
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function position(string | Closure | null $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->evaluate($this->position);
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

    public function transformOptionsForJsUsing(?Closure $callback): static
    {
        $this->transformOptionsForJsUsing = $callback;

        return $this;
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
            } elseif ($state instanceof Stringable) {
                $state = (string) $state;
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

    public function rememberOptions(bool | Closure $condition = true): static
    {
        $this->areOptionsRemembered = $condition;

        return $this;
    }

    public function areOptionsRemembered(): bool
    {
        return (bool) $this->evaluate($this->areOptionsRemembered);
    }

    /**
     * @return array<string>
     */
    public function getOptionsSearchResults(string $search): array
    {
        if (! $this->getOptionsSearchResultsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getOptionsSearchResultsUsing, [
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
    public function getOptionsSearchResultsForJs(string $search): array
    {
        return $this->transformOptionsForJs($this->getOptionsSearchResults($search));
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

    public function hasDynamicOptions(): bool
    {
        if ($this->hasDynamicDisabledOptions()) {
            return true;
        }

        return $this->options instanceof Closure;
    }

    public function hasInitialNoOptionsMessage(): bool
    {
        if ($this->hasOptionsRelationship()) {
            return $this->areOptionsPreloaded();
        }

        return ! $this->hasDynamicOptionsSearchResults();
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

    public function preloadOptions(bool | Closure $condition = true): static
    {
        $this->areOptionsPreloaded = $condition;

        return $this;
    }

    public function areOptionsPreloaded(): bool
    {
        return (bool) $this->evaluate($this->areOptionsPreloaded);
    }

    public function hasDynamicOptionsSearchResults(): bool
    {
        return $this->getOptionsSearchResultsUsing instanceof Closure;
    }

    /**
     * @return array<string>
     */
    public function getOptionsSearchColumns(): ?array
    {
        $columns = $this->optionsSearchColumns;

        if ($this->hasOptionsRelationship() && (filled($relationshipTitleAttribute = $this->getOptionsRelationshipTitleAttribute()))) {
            $columns ??= [$relationshipTitleAttribute];
        }

        return $columns;
    }

    public function optionsRelationship(string | Closure $name, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null): static
    {
        $this->optionsRelationship = $name;
        $this->optionsRelationshipTitleAttribute = $titleAttribute;
        $this->modifyOptionsRelationshipQueryUsing = $modifyQueryUsing;

        $this->getOptionsSearchResultsUsing(static function (SelectColumn $column, ?string $search) use ($modifyQueryUsing): array {
            $relationship = Relation::noConstraints(fn () => $column->getOptionsRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($modifyQueryUsing) {
                $relationshipQuery = $column->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => $search,
                ]) ?? $relationshipQuery;
            }

            $column->applyOptionsSearchConstraint(
                $relationshipQuery,
                generate_search_term_expression($search, $column->isOptionsSearchForcedCaseInsensitive(), $relationshipQuery->getConnection()),
            );

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $column->optionsLimit($baseRelationshipQuery->limit);
            } else {
                $relationshipQuery->limit($column->getOptionsLimit());
            }

            $qualifiedRelatedKeyName = $column->getQualifiedRelatedKeyNameForOptionsRelationship($relationship);

            if ($column->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $column->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $column->getOptionsRelationshipTitleAttribute();

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

        $this->options(static function (SelectColumn $column) use ($modifyQueryUsing): ?array {
            if (($column->areOptionsSearchable()) && ! $column->areOptionsPreloaded()) {
                return null;
            }

            $relationship = Relation::noConstraints(fn () => $column->getOptionsRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($modifyQueryUsing) {
                $relationshipQuery = $column->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => null,
                ]) ?? $relationshipQuery;
            }

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $column->optionsLimit($baseRelationshipQuery->limit);
            } elseif ($column->isSearchable() && filled($column->getOptionsSearchColumns())) {
                $relationshipQuery->limit($column->getOptionsLimit());
            }

            $qualifiedRelatedKeyName = $column->getQualifiedRelatedKeyNameForOptionsRelationship($relationship);

            if ($column->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($qualifiedRelatedKeyName, '.')} => $column->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $column->getOptionsRelationshipTitleAttribute();

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

        $this->getOptionLabelUsing(static function (SelectColumn $column, Model $record, $state) use ($modifyQueryUsing) {
            $relationship = Relation::noConstraints(fn () => $column->getOptionsRelationship());

            $record = $record->getRelationValue($column->getOptionsRelationshipName());

            if (strval($record?->getAttribute($column->getRelatedKeyNameForOptionsRelationship($relationship))) !== strval($state)) {
                $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                $relationshipQuery->where($column->getQualifiedRelatedKeyNameForOptionsRelationship($relationship), $state);

                if ($modifyQueryUsing) {
                    $relationshipQuery = $column->evaluate($modifyQueryUsing, [
                        'query' => $relationshipQuery,
                        'search' => null,
                    ]) ?? $relationshipQuery;
                }

                $record = $relationshipQuery->first();
            }

            if (! $record) {
                return null;
            }

            if ($column->hasOptionLabelFromRecordUsingCallback()) {
                return $column->getOptionLabelFromRecord($record);
            }

            $relationshipTitleAttribute = $column->getOptionsRelationshipTitleAttribute();

            if (str_contains($relationshipTitleAttribute, '->')) {
                $relationshipTitleAttribute = str_replace('->', '.', $relationshipTitleAttribute);
            }

            return data_get($record, $relationshipTitleAttribute);
        });

        $this->rememberOptions();

        return $this;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getOptions(): array
    {
        if ($this->areOptionsRemembered()) {
            return $this->rememberedOptions ??= $this->getBaseOptions();
        }

        return $this->getBaseOptions();
    }

    public function forceOptionsSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isOptionsSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isOptionsSearchForcedCaseInsensitive(): ?bool
    {
        return $this->evaluate($this->isOptionsSearchForcedCaseInsensitive);
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function applyOptionsSearchConstraint(Builder $query, string $search): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $isForcedCaseInsensitive = $this->isOptionsSearchForcedCaseInsensitive();

        $query->where(function (Builder $query) use ($databaseConnection, $isForcedCaseInsensitive, $search): Builder {
            $isFirst = true;

            foreach ($this->getOptionsSearchColumns() ?? [] as $searchColumn) {
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

    public function getOptionsRelationship(): BelongsTo | BelongsToMany | HasOneOrMany | HasOneOrManyThrough | BelongsToThrough | null
    {
        if (! $this->hasOptionsRelationship()) {
            return null;
        }

        $record = $this->getRecord();

        $relationship = null;

        $relationshipName = $this->getOptionsRelationshipName();

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
            $model = $record::class;

            throw new LogicException("The relationship [{$relationshipName}] does not exist on the model [{$model}].");
        }

        return $relationship;
    }

    public function getOptionsRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->optionsRelationshipTitleAttribute);
    }

    public function getOptionsRelationshipName(): ?string
    {
        return $this->evaluate($this->optionsRelationship);
    }

    public function hasOptionsRelationship(): bool
    {
        return filled($this->getOptionsRelationshipName());
    }

    public function getRelatedKeyNameForOptionsRelationship(Relation $relationship): string
    {
        if ($relationship instanceof BelongsToMany) {
            return $relationship->getRelatedKeyName();
        }

        if ($relationship instanceof HasOneOrManyThrough) {
            return $relationship->getForeignKeyName();
        }

        if (
            ($relationship instanceof HasOneOrMany) ||
            ($relationship instanceof BelongsToThrough)
        ) {
            return $relationship->getRelated()->getKeyName();
        }

        /** @var BelongsTo $relationship */

        return $relationship->getOwnerKeyName();
    }

    public function getQualifiedRelatedKeyNameForOptionsRelationship(Relation $relationship): string
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

    public function applyEagerLoading(EloquentBuilder | Relation $query): EloquentBuilder | Relation
    {
        if ($this->hasOptionsRelationship()) {
            $relationshipName = $this->getOptionsRelationshipName();

            if (! array_key_exists($relationshipName, $query->getEagerLoads())) {
                $query = $query->with($this->modifyOptionsRelationshipQueryUsing ? [$relationshipName => $this->modifyOptionsRelationshipQueryUsing] : [$relationshipName]);
            }
        }

        return parent::applyEagerLoading($query);
    }

    public function toEmbeddedHtml(): string
    {
        $canSelectPlaceholder = $this->canSelectPlaceholder();
        $isDisabled = $this->isDisabled();
        $isNative = ! $this->areOptionsSearchable() && $this->isNative();
        $name = $this->getName();
        $options = $this->getOptions();
        $placeholder = $this->getPlaceholder();
        $recordKey = $this->getRecordKey();
        $state = $this->getState();

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'x-load' => true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/select', 'filament/tables'),
                'x-data' => 'selectTableColumn({
                    canOptionLabelsWrap: ' . Js::from($this->canOptionLabelsWrap()) . ',
                    canSelectPlaceholder: ' . Js::from($canSelectPlaceholder) . ',
                    getOptionLabelUsing: async () => {
                        return await $wire.callTableColumnMethod(' . Js::from($name) . ', ' . Js::from($recordKey) . ', \'getOptionLabel\')
                    },
                    getOptionsUsing: async () => {
                        return await $wire.callTableColumnMethod(
                            ' . Js::from($name) . ',
                            ' . Js::from($recordKey) . ',
                            \'getOptionsForJs\',
                        )
                    },
                    getSearchResultsUsing: async (search) => {
                        return await $wire.callTableColumnMethod(
                            ' . Js::from($name) . ',
                            ' . Js::from($recordKey) . ',
                            \'getOptionsSearchResultsForJs\',
                            { search },
                        )
                    },
                    hasDynamicOptions: ' . Js::from($this->hasDynamicOptions()) . ',
                    hasDynamicSearchResults: ' . Js::from($this->hasDynamicOptionsSearchResults()) . ',
                    hasInitialNoOptionsMessage: ' . Js::from($this->hasInitialNoOptionsMessage()) . ',
                    initialOptionLabel: ' . Js::from($this->getOptionLabel()) . ',
                    isDisabled: ' . Js::from($isDisabled) . ',
                    isHtmlAllowed: ' . Js::from($this->isOptionsHtmlAllowed()) . ',
                    isNative: ' . Js::from($isNative) . ',
                    isSearchable: ' . Js::from($this->areOptionsSearchable()) . ',
                    loadingMessage: ' . Js::from($this->getOptionsLoadingMessage()) . ',
                    name: ' . Js::from($name) . ',
                    noOptionsMessage: ' . Js::from($this->getNoOptionsMessage()) . ',
                    noSearchResultsMessage: ' . Js::from($this->getNoOptionsSearchResultsMessage()) . ',
                    options: ' . Js::from($isNative ? [] : $this->getOptionsForJs()) . ',
                    optionsLimit: ' . Js::from($this->getOptionsLimit()) . ',
                    placeholder: ' . Js::from($placeholder) . ',
                    position: ' . Js::from($this->getPosition()) . ',
                    recordKey: ' . Js::from($recordKey) . ',
                    searchableOptionFields: ' . Js::from($this->getSearchableOptionFields()) . ',
                    searchDebounce: ' . Js::from($this->getOptionsSearchDebounce()) . ',
                    searchingMessage: ' . Js::from($this->getOptionsSearchingMessage()) . ',
                    searchPrompt: ' . Js::from($this->getOptionsSearchPrompt()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-select',
                'fi-inline' => $this->isInline(),
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'disabled' => $isDisabled,
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
                'x-bind:disabled' => $isDisabled ? null : 'isLoading',
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                        allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-select-input',
            ]);

        ob_start(); ?>

        <div
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= str(($state instanceof BackedEnum) ? $state->value : $state)->replace('"', '\\"') ?>" x-ref="serverState" />

            <div
                x-bind:class="{
                    'fi-disabled': isLoading || <?= Js::from($isDisabled) ?>,
                    'fi-invalid': error !== undefined,
                }"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                x-on:click.prevent.stop
                <?php if (! $isNative) { ?>
                    wire:ignore
                    x-on:keydown.esc="select.dropdown.isActive && $event.stopPropagation()"
                <?php } ?>
                class="fi-input-wrp"
            >
                <?php if ($isNative) { ?>
                    <select
                        x-model="state"
                        <?= $inputAttributes->toHtml() ?>
                    >
                        <?php if ($canSelectPlaceholder) { ?>
                            <option value=""><?= $placeholder ?></option>
                        <?php } ?>

                        <?php foreach ($options as $value => $label) { ?>
                            <option
                                <?= $this->isOptionDisabled($value, $label) ? 'disabled' : null ?>
                                value="<?= $value ?>"
                            >
                                <?= $label ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <div class="fi-select-input">
                        <div x-ref="select"></div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
