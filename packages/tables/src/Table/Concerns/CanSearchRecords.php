<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\apply_search_constraint;
use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_pattern;
use function Filament\Support\generate_search_term_expression;

trait CanSearchRecords
{
    protected ?bool $isSearchable = null;

    /**
     * @var array<string | Closure>
     */
    protected array $extraSearchableColumns = [];

    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchesInSession = false;

    protected string | Closure | null $searchPlaceholder = null;

    protected ?string $searchDebounce = null;

    protected bool | Closure $isSearchOnBlur = false;

    protected bool | Closure $shouldSplitSearchTerms = true;

    protected ?Closure $searchUsing = null;

    public function persistSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsSearchInSession = $condition;

        return $this;
    }

    public function persistColumnSearchesInSession(bool | Closure $condition = true): static
    {
        $this->persistsColumnSearchesInSession = $condition;

        return $this;
    }

    /**
     * @param  bool | array<string | Closure>  $condition
     */
    public function searchable(bool | array $condition = true): static
    {
        if ($condition === true) {
            $this->isSearchable = true;
        } elseif (! $condition) {
            $this->isSearchable = false;
            $this->extraSearchableColumns = [];
        } else {
            $this->isSearchable = true;
            $this->extraSearchableColumns = $condition;
        }

        return $this;
    }

    public function searchDebounce(?string $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    public function isSearchable(): bool
    {
        if (is_bool($this->isSearchable)) {
            return $this->isSearchable;
        }

        if ($this->getExtraSearchableColumns()) {
            return true;
        }

        foreach ($this->getColumns() as $column) {
            if (! $column->isGloballySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isSearchableByColumn(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            if ($column->isHidden()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function persistsSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsSearchInSession);
    }

    public function persistsColumnSearchesInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsColumnSearchesInSession);
    }

    public function searchPlaceholder(string | Closure | null $searchPlaceholder): static
    {
        $this->searchPlaceholder = $searchPlaceholder;

        return $this;
    }

    public function getSearchPlaceholder(): ?string
    {
        return $this->evaluate($this->searchPlaceholder);
    }

    public function hasSearch(): bool
    {
        return $this->getLivewire()->hasTableSearch();
    }

    public function getSearchIndicator(): Indicator
    {
        return $this->getLivewire()->getTableSearchIndicator();
    }

    /**
     * @return array<Indicator> | array<string, string>
     */
    public function getColumnSearchIndicators(): array
    {
        return $this->getLivewire()->getTableColumnSearchIndicators();
    }

    public function getSearchDebounce(): string
    {
        return $this->searchDebounce ?? '500ms';
    }

    public function searchOnBlur(bool | Closure $condition = true): static
    {
        $this->isSearchOnBlur = $condition;

        return $this;
    }

    public function isSearchOnBlur(): bool
    {
        return (bool) $this->evaluate($this->isSearchOnBlur);
    }

    /**
     * @return array<string | Closure>
     */
    public function getExtraSearchableColumns(): array
    {
        return $this->extraSearchableColumns;
    }

    public function applyExtraSearchConstraints(Builder $query, string $search, bool &$isFirst): void
    {
        foreach ($this->getExtraSearchableColumns() as $column) {
            if (blank($column)) {
                continue;
            }

            $whereClause = $isFirst ? 'where' : 'orWhere';

            if ($column instanceof Closure) {
                $query->{$whereClause}(
                    fn ($query) => $this->evaluate($column, [
                        'query' => $query,
                        'search' => $search,
                        'searchQuery' => $search,
                    ]),
                );

                $isFirst = false;

                continue;
            }

            /** @var Connection $databaseConnection */
            $databaseConnection = $query->getConnection();

            $model = $query->getModel();

            $nonTranslatableSearch = generate_search_term_expression($search, isSearchForcedCaseInsensitive: null, databaseConnection: $databaseConnection);
            $searchPattern = generate_search_pattern($nonTranslatableSearch, hasLeadingWildcard: true, hasTrailingWildcard: true, databaseConnection: $databaseConnection);

            $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $column),
                fn (Builder $query): Builder => $translatableContentDriver->applySearchConstraintToQuery($query, $column, $search, $whereClause),
                fn (Builder $query) => $query->when(
                    $this->getExtraSearchableColumnRelationship($column, $query->getModel()),
                    fn (Builder $query): Builder => $query->{"{$whereClause}Has"}(
                        (string) str($column)->beforeLast('.'),
                        function (Builder $query) use ($column, $search): Builder {
                            /** @var Connection $databaseConnection */
                            $databaseConnection = $query->getConnection();

                            $search = generate_search_term_expression($search, isSearchForcedCaseInsensitive: null, databaseConnection: $databaseConnection);

                            return apply_search_constraint(
                                $query,
                                generate_search_column_expression((string) str($column)->afterLast('.'), isSearchForcedCaseInsensitive: null, databaseConnection: $databaseConnection),
                                generate_search_pattern($search, hasLeadingWildcard: true, hasTrailingWildcard: true, databaseConnection: $databaseConnection),
                            );
                        },
                    ),
                    function (Builder $query) use ($databaseConnection, $searchPattern, $column, $whereClause): Builder {
                        // Treat the missing "relationship" as a JSON column if dot notation is used in the column name.
                        if (str($column)->contains('.')) {
                            $column = (string) str($column)->replace('.', '->');
                        }

                        return apply_search_constraint(
                            $query,
                            generate_search_column_expression($column, isSearchForcedCaseInsensitive: null, databaseConnection: $databaseConnection),
                            $searchPattern,
                            ($whereClause === 'where') ? 'and' : 'or',
                        );
                    },
                ),
            );

            $isFirst = false;
        }
    }

    public function getExtraSearchableColumnRelationship(string $name, Model $record): ?Relation
    {
        if (blank($name) || (! str($name)->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (str($name)->beforeLast('.')->explode('.')->all() as $nestedRelationshipName) {
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

        return $relationship;
    }

    public function splitSearchTerms(bool | Closure $condition = true): static
    {
        $this->shouldSplitSearchTerms = $condition;

        return $this;
    }

    public function shouldSplitSearchTerms(): bool
    {
        return (bool) $this->evaluate($this->shouldSplitSearchTerms);
    }

    public function searchUsing(?Closure $searchUsing): static
    {
        $this->searchUsing = $searchUsing;

        return $this;
    }

    public function hasSearchUsingCallback(): bool
    {
        return filled($this->searchUsing);
    }

    public function callSearchUsing(Builder $query, string $search): void
    {
        $this->evaluate($this->searchUsing, [
            'query' => $query,
            'search' => $search,
        ]);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGlobalSearchHtml(): string
    {
        ob_start();

        if ($this->isSearchable()) {
            $searchPlaceholder = $this->getSearchPlaceholder();
            ?>

            <?= $this->generateSearchFieldHtml(
                debounce: $this->getSearchDebounce(),
                onBlur: $this->isSearchOnBlur(),
                placeholder: $searchPlaceholder,
                attributes: new ComponentAttributeBag([]),
            ); ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnSearchesHtml(): string
    {
        $records = $this->getRecords();

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->isSearchableByColumn()) { ?>
            <tbody>
            <?php
            $breakpointOrder = [
                'base' => 0,
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ];
            $responsiveBreakpointOrder = array_diff_key($breakpointOrder, ['base' => true]);
            $individualSearchHiddenAt = [];

            $individualSearchColumnStates = [];

            foreach ($this->getVisibleColumns() as $columnKey => $column) {
                $individualSearchColumnStates[$columnKey] = [
                    'hiddenFrom' => $column->getHiddenFrom(),
                    'isIndividuallySearchable' => $column->isIndividuallySearchable(),
                    'visibleFrom' => $column->getVisibleFrom(),
                ];
            }

            foreach ($breakpointOrder as $breakpoint => $breakpointIndex) {
                foreach ($individualSearchColumnStates as $columnState) {
                    if (! $columnState['isIndividuallySearchable']) {
                        continue;
                    }

                    $visibleFromIndex = filled($columnState['visibleFrom']) ? ($responsiveBreakpointOrder[$columnState['visibleFrom']] ?? null) : null;

                    if ($visibleFromIndex !== null) {
                        if ($breakpointIndex >= $visibleFromIndex) {
                            continue 2;
                        }

                        continue;
                    }

                    $hiddenFromIndex = filled($columnState['hiddenFrom']) ? ($responsiveBreakpointOrder[$columnState['hiddenFrom']] ?? null) : null;

                    if (($hiddenFromIndex === null) || ($breakpointIndex < $hiddenFromIndex)) {
                        continue 2;
                    }
                }

                $individualSearchHiddenAt[] = $breakpoint;
            }
            ?>

                <tr
                    <?php if (filled($individualSearchHiddenAt)) { ?>
                        data-search-hidden-at="<?= e(implode(' ', $individualSearchHiddenAt)); ?>"
                    <?php } ?>
                    class="fi-ta-row fi-ta-row-not-reorderable fi-ta-individual-search-row"
                >
                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?php if (count($records)) { ?>
                        <?php if ($this->isReordering()) { ?>
                            <td></td>
                        <?php } else { ?>
                            <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])) { ?>
                                <td></td>
                            <?php } ?>

                            <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                                <td></td>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $columnKey => $column) {
                        $columnName = $column->getName();
                        $columnState = $individualSearchColumnStates[$columnKey];
                        $isIndividuallySearchable = $columnState['isIndividuallySearchable'];
                        $columnHiddenFrom = $columnState['hiddenFrom'];
                        $columnVisibleFrom = $columnState['visibleFrom'];
                        ?>

                        <td
                            class="<?= Arr::toCssClasses([
                                'fi-ta-cell',
                                'fi-ta-individual-search-cell' => $isIndividuallySearchable,
                                'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                                filled($columnHiddenFrom) ? "{$columnHiddenFrom}:fi-hidden" : '',
                                filled($columnVisibleFrom) ? "{$columnVisibleFrom}:fi-visible" : '',
                            ]); ?>"
                        >
                            <?php if ($isIndividuallySearchable) { ?>
                                <?= $this->generateSearchFieldHtml(
                                    debounce: $this->getSearchDebounce(),
                                    label: $column->getLabel(),
                                    labelHidden: ! $this->isStackedOnMobile(),
                                    onBlur: $this->isSearchOnBlur(),
                                    wireModel: 'tableColumnSearches.' . $columnName,
                                    attributes: new ComponentAttributeBag([]),
                                ); ?>
                            <?php } ?>
                        </td>
                    <?php } ?>

                    <?php if ((! $this->isReordering()) && count($records)) { ?>
                        <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) { ?>
                            <td></td>
                        <?php } ?>

                        <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                            <td></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            </tbody>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSearchFieldHtml(
        string | int $debounce = '500ms',
        string | Htmlable | null $label = null,
        bool $labelHidden = true,
        bool $onBlur = false,
        ?string $placeholder = null,
        string $wireModel = 'tableSearch',
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;
        $label ??= __('filament-tables::table.fields.search.label');
        $placeholder ??= __('filament-tables::table.fields.search.placeholder');
        $livewire = $this->getLivewire();

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.search-field')) {
            return view('filament-tables::components.search-field', [
                'debounce' => $debounce,
                'label' => $label,
                'labelHidden' => $labelHidden,
                'onBlur' => $onBlur,
                'placeholder' => $placeholder,
                'wireModel' => $wireModel,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $livewire,
            ])->render();
        }

        $wireModelAttribute = $onBlur ? 'wire:model.blur' : "wire:model.live.debounce.{$debounce}";
        $inputKey = $livewire->getId() . '.table.' . $wireModel . '.field.input';
        $this->setRenderKey($inputKey);

        $inputHtml = $this->generateInputHtml(
            attributes: (new ComponentAttributeBag)->merge([
                'autocomplete' => 'off',
                'maxlength' => 1000,
                'placeholder' => $placeholder,
                'type' => 'search',
                'wire:key' => $inputKey,
                $wireModelAttribute => $wireModel,
                'x-bind:id' => '$id(\'input\')',
                'x-on:keyup' => '$event.key === \'Enter\' && $wire.$refresh()',
            ], escape: false),
            inlinePrefix: true,
        );

        return '<div x-id="[\'input\']" ' . $attributes->class(['fi-ta-search-field'])->toHtml() . '>'
            . '<label x-bind:for="$id(\'input\')" class="' . ($labelHidden ? 'fi-sr-only' : 'fi-ta-cell-label') . '">' . e($label) . '</label>'
            . $this->generateInputWrapperHtml(
                attributes: new FilamentComponentAttributeBag(['wire:target' => $wireModel]),
                slot: new HtmlString($inputHtml),
                inlinePrefix: true,
                prefixIcon: Heroicon::MagnifyingGlass,
                prefixIconAlias: TablesIconAlias::SEARCH_FIELD,
            )
            . '</div>';
    }
}
