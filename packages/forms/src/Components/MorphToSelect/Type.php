<?php

namespace Filament\Forms\Components\MorphToSelect;

use Closure;
use Exception;
use Filament\Forms\Components\Select;
use function Filament\Support\get_model_label;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Type
{
    protected ?string $label = null;

    public Closure $getOptionLabelUsing;

    public Closure $getSearchResultsUsing;

    public Closure $getOptionsUsing;

    protected ?Closure $modifyOptionsQueryUsing = null;

    protected ?array $searchColumns = null;

    protected ?string $titleColumnName = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected int $optionsLimit = 50;

    protected string $model;

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

            if (empty($query->getQuery()->orders)) {
                $query->orderBy($this->getTitleColumnName());
            }

            $search = strtolower($search);

            /** @var Connection $databaseConnection */
            $databaseConnection = $query->getConnection();

            $searchOperator = match ($databaseConnection->getDriverName()) {
                'pgsql' => 'ilike',
                default => 'like',
            };

            $isFirst = true;

            $query->where(function (Builder $query) use ($isFirst, $searchOperator, $search): Builder {
                foreach ($this->getSearchColumns() as $searchColumnName) {
                    $whereClause = $isFirst ? 'where' : 'orWhere';

                    $query->{$whereClause}(
                        $searchColumnName,
                        $searchOperator,
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

            return $query
                ->pluck($this->getTitleColumnName(), $keyName)
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

            if (empty($query->getQuery()->orders)) {
                $query->orderBy($this->getTitleColumnName());
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

            return $query
                ->pluck($this->getTitleColumnName(), $keyName)
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

            return $record->getAttributeValue($this->getTitleColumnName());
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

    public function titleColumnName(?string $name): static
    {
        $this->titleColumnName = $name;

        return $this;
    }

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

    public function getSearchColumns(): ?array
    {
        return $this->searchColumns ?? [$this->getTitleColumnName()];
    }

    public function getTitleColumnName(): string
    {
        if (blank($this->titleColumnName)) {
            throw new Exception("MorphToSelect type [{$this->getModel()}] must have a [titleColumnName()] set.");
        }

        return $this->titleColumnName;
    }

    public function getOptionsLimit(): int
    {
        return $this->optionsLimit;
    }
}
