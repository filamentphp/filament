<?php

namespace Filament\Forms\Components\MorphToSelect;

use Closure;
use Exception;
use Filament\Forms\Components\Select;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;
use function Filament\Support\get_model_label;

class Type
{
    protected ?string $label = null;

    public Closure $getOptionLabelUsing;

    public Closure $getSearchResultsUsing;

    public Closure $getOptionsUsing;

    protected ?Closure $modifyOptionsQueryUsing = null;

    /**
     * @var array<string> | null
     */
    protected ?array $searchColumns = null;

    protected ?string $titleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected int $optionsLimit = 50;

    protected string $model;

    protected ?bool $isSearchForcedCaseInsensitive = null;

    final public function __construct(string $model)
    {
        $this->model($model);

        $this->setUp();
    }

    public static function make(string $model): static
    {
        return app(static::class, ['model' => $model]);
    }

    protected function setUp(): void
    {
        $this->getSearchResultsUsing(function (Select $component, ?string $search): array {
            $query = $this->getModel()::query();

            if ($this->modifyOptionsQueryUsing) {
                $query = $component->evaluate($this->modifyOptionsQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            /** @var Connection $databaseConnection */
            $databaseConnection = $query->getConnection();

            $isForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive();

            $isFirst = true;

            $search = generate_search_term_expression($search, $isForcedCaseInsensitive, $databaseConnection);

            $query->where(function (Builder $query) use ($isFirst, $isForcedCaseInsensitive, $databaseConnection, $search): Builder {
                foreach ($this->getSearchColumns() as $searchColumn) {
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

            $baseQuery = $query->getQuery();

            if (isset($baseQuery->limit)) {
                $component->optionsLimit($baseQuery->limit);
            } else {
                $query->limit($component->getOptionsLimit());
            }

            $keyName = $query->getModel()->getKeyName();

            if ($this->hasOptionLabelFromRecordUsingCallback()) {
                return $query
                    ->get()
                    ->mapWithKeys(fn (Model $record) => [
                        $record->{$keyName} => $this->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $titleAttribute = $this->getTitleAttribute();

            if (empty($query->getQuery()->orders)) {
                $query->orderBy($titleAttribute);
            }

            return $query
                ->pluck($titleAttribute, $keyName)
                ->toArray();
        });

        $this->getOptionsUsing(function (Select $component): ?array {
            if (($component->isSearchable()) && ! $component->isPreloaded()) {
                return null;
            }

            $query = $this->getModel()::query();

            if ($this->modifyOptionsQueryUsing) {
                $query = $component->evaluate($this->modifyOptionsQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            $keyName = $query->getModel()->getKeyName();

            if ($this->hasOptionLabelFromRecordUsingCallback()) {
                return $query
                    ->get()
                    ->mapWithKeys(fn (Model $record) => [
                        $record->{$keyName} => $this->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $titleAttribute = $this->getTitleAttribute();

            if (empty($query->getQuery()->orders)) {
                $query->orderBy($titleAttribute);
            }

            return $query
                ->pluck($titleAttribute, $keyName)
                ->toArray();
        });

        $this->getOptionLabelUsing(function (Select $component, $value) {
            $query = $this->getModel()::query();

            $query->where($query->getModel()->getKeyName(), $value);

            if ($this->modifyOptionsQueryUsing) {
                $query = $component->evaluate($this->modifyOptionsQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            $record = $query->first();

            if (! $record) {
                return null;
            }

            if ($this->hasOptionLabelFromRecordUsingCallback()) {
                return $this->getOptionLabelFromRecord($record);
            }

            return $record->getAttributeValue($this->getTitleAttribute());
        });
    }

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function titleAttribute(?string $name): static
    {
        $this->titleAttribute = $name;

        return $this;
    }

    /**
     * @deprecated Use `titleAttribute()` instead.
     */
    public function titleColumnName(?string $name): static
    {
        $this->titleAttribute($name);

        return $this;
    }

    /**
     * @param  array<string> | null  $columns
     */
    public function searchColumns(?array $columns): static
    {
        $this->searchColumns = $columns;

        return $this;
    }

    public function modifyOptionsQueryUsing(?Closure $callback): static
    {
        $this->modifyOptionsQueryUsing = $callback;

        return $this;
    }

    public function getOptionsUsing(Closure $callback): static
    {
        $this->getOptionsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function getOptionLabelUsing(Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function getOptionLabelFromRecord(Model $record): string
    {
        return ($this->getOptionLabelFromRecordUsing)($record);
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getLabel(): string
    {
        return $this->label ?? Str::ucfirst(get_model_label($this->getModel()));
    }

    public function getAlias(): string
    {
        return app($this->getModel())->getMorphClass();
    }

    /**
     * @return array<string>
     */
    public function getSearchColumns(): ?array
    {
        return $this->searchColumns ?? [$this->getTitleAttribute()];
    }

    public function getTitleAttribute(): string
    {
        if (blank($this->titleAttribute)) {
            throw new Exception("MorphToSelect type [{$this->getModel()}] must have a [titleAttribute()] set.");
        }

        return $this->titleAttribute;
    }

    public function getOptionsLimit(): int
    {
        return $this->optionsLimit;
    }

    public function forceSearchCaseInsensitive(?bool $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isSearchForcedCaseInsensitive(): ?bool
    {
        return $this->isSearchForcedCaseInsensitive;
    }
}
