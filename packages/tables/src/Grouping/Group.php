<?php

namespace Filament\Tables\Grouping;

use BackedEnum;
use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Filament\Support\Components\Component;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class Group extends Component
{
    protected ?string $column;

    protected ?Closure $getDescriptionFromRecordUsing = null;

    protected ?Closure $getKeyFromRecordUsing = null;

    protected ?Closure $getTitleFromRecordUsing = null;

    protected ?Closure $groupQueryUsing = null;

    protected ?Closure $orderQueryUsing = null;

    protected ?Closure $scopeQueryUsing = null;

    protected ?Closure $scopeQueryByKeyUsing = null;

    protected ?string $label;

    protected string $id;

    protected bool $isCollapsible = false;

    protected bool $isTitlePrefixedWithLabel = true;

    protected bool $isDate = false;

    final public function __construct(?string $id = null)
    {
        $this->id($id);
    }

    public static function make(?string $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    public function collapsible(bool $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function column(?string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function date(bool $condition = true): static
    {
        $this->isDate = $condition;

        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescriptionFromRecordUsing(?Closure $callback): static
    {
        $this->getDescriptionFromRecordUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use the `getDescriptionFromRecordUsing()` method instead.
     */
    public function getDescriptionUsing(?Closure $callback): static
    {
        $this->getDescriptionFromRecordUsing($callback);

        return $this;
    }

    public function getTitleFromRecordUsing(?Closure $callback): static
    {
        $this->getTitleFromRecordUsing = $callback;

        return $this;
    }

    public function getKeyFromRecordUsing(?Closure $callback): static
    {
        $this->getKeyFromRecordUsing = $callback;

        return $this;
    }

    public function groupQueryUsing(?Closure $callback): static
    {
        $this->groupQueryUsing = $callback;

        return $this;
    }

    public function orderQueryUsing(?Closure $callback): static
    {
        $this->orderQueryUsing = $callback;

        return $this;
    }

    public function scopeQueryUsing(?Closure $callback): static
    {
        $this->scopeQueryUsing = $callback;

        return $this;
    }

    public function scopeQueryByKeyUsing(?Closure $callback): static
    {
        $this->scopeQueryByKeyUsing = $callback;

        return $this;
    }

    public function titlePrefixedWithLabel(bool $condition = true): static
    {
        $this->isTitlePrefixedWithLabel = $condition;

        return $this;
    }

    public function isCollapsible(): bool
    {
        return $this->isCollapsible;
    }

    public function isTitlePrefixedWithLabel(): bool
    {
        return $this->isTitlePrefixedWithLabel;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label ?? (string) str($this->getId())
            ->beforeLast('.')
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getDescription(Model $record, ?string $title): ?string
    {
        if (! $this->getDescriptionFromRecordUsing) {
            return null;
        }

        return $this->evaluate(
            $this->getDescriptionFromRecordUsing,
            namedInjections: [
                'record' => $record,
                'title' => $title,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getStringKey(Model $record): ?string
    {
        $key = $this->getKey($record);

        if ($key instanceof BackedEnum) {
            $key = $key->value;
        }

        if (filled($key) && $this->isDate()) {
            if (! ($key instanceof DateTimeInterface)) {
                $key = Carbon::parse($key);
            }

            $key = $key->format('Y-m-d');
        }

        return filled($key) ? strval($key) : null;
    }

    public function getKey(Model $record): mixed
    {
        $column = $this->getColumn();

        if ($this->getKeyFromRecordUsing) {
            return $this->evaluate(
                $this->getKeyFromRecordUsing,
                namedInjections: [
                    'column' => $column,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        }

        return Arr::get($record, $this->getColumn());
    }

    public function getTitle(Model $record): ?string
    {
        $column = $this->getColumn();

        if ($this->getTitleFromRecordUsing) {
            $title = $this->evaluate(
                $this->getTitleFromRecordUsing,
                namedInjections: [
                    'column' => $column,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            );
        } else {
            $title = Arr::get($record, $column);
        }

        if ($title instanceof LabelInterface) {
            $title = $title->getLabel();
        }

        if (filled($title) && $this->isDate()) {
            if (! ($title instanceof DateTimeInterface)) {
                $title = Carbon::parse($title);
            }

            $title = $title->format(Table::$defaultDateDisplayFormat);
        }

        return $title;
    }

    public function groupQuery(Builder $query, Model $model): Builder
    {
        if ($this->groupQueryUsing) {
            return $this->evaluate($this->groupQueryUsing, [
                'column' => $this->getColumn(),
                'query' => $query,
            ]) ?? $query;
        }

        if ($relationship = $this->getRelationship($model)) {
            $column = $relationship->getRelated()->qualifyColumn($this->getRelationshipAttribute());
        } else {
            $column = $this->getColumn();
        }

        if ($this->isDate()) {
            return $query->groupByRaw("date({$column})");
        }

        return $query->groupBy($column);
    }

    public function orderQuery(EloquentBuilder $query, string $direction): EloquentBuilder
    {
        if ($this->orderQueryUsing) {
            return $this->evaluate($this->orderQueryUsing, [
                'column' => $this->getColumn(),
                'direction' => $direction,
                'query' => $query,
            ]) ?? $query;
        }

        return $query->orderBy($this->getSortColumnForQuery($query, $this->getRelationshipAttribute()), $direction);
    }

    /**
     * @param  array<string> | null  $relationships
     */
    protected function getSortColumnForQuery(EloquentBuilder $query, string $sortColumn, ?array $relationships = null, ?Relation $lastRelationship = null): string | Builder
    {
        $relationships ??= ($relationshipName = $this->getRelationshipName()) ?
            explode('.', $relationshipName) :
            [];

        if (! count($relationships)) {
            return $lastRelationship ? $lastRelationship->getQuery()->getModel()->qualifyColumn($sortColumn) : $sortColumn;
        }

        $currentRelationshipName = array_shift($relationships);

        $relationship = $this->getRelationship($query->getModel(), $currentRelationshipName);

        $relatedQuery = $relationship->getRelated()::query();

        return $relationship
            ->getRelationExistenceQuery(
                $relatedQuery,
                $query,
                [$currentRelationshipName => $this->getSortColumnForQuery(
                    $relatedQuery,
                    $sortColumn,
                    $relationships,
                    $relationship,
                )],
            )
            ->applyScopes()
            ->getQuery();
    }

    public function scopeQuery(EloquentBuilder $query, Model $record): EloquentBuilder
    {
        if ($this->scopeQueryUsing) {
            return $this->evaluate(
                $this->scopeQueryUsing,
                namedInjections: [
                    'column' => $this->getColumn(),
                    'query' => $query,
                    'record' => $record,
                ],
                typedInjections: [
                    Model::class => $record,
                    $record::class => $record,
                ],
            ) ?? $query;
        }

        $this->scopeQueryByKey($query, $this->getKey($record));

        return $query;
    }

    public function scopeQueryByKey(EloquentBuilder $query, string $key): EloquentBuilder
    {
        $column = $this->getColumn();

        if ($this->scopeQueryByKeyUsing) {
            return $this->evaluate(
                $this->scopeQueryByKeyUsing,
                namedInjections: [
                    'column' => $column,
                    'key' => $key,
                    'query' => $query,
                ],
            ) ?? $query;
        }

        if ($relationshipName = $this->getRelationshipName()) {
            return $query->whereHas(
                $relationshipName,
                fn (EloquentBuilder $query) => $this->applyDefaultScopeToQuery($query, $this->getRelationshipAttribute(), $key),
            );
        }

        return $this->applyDefaultScopeToQuery($query, $column, $key);
    }

    protected function applyDefaultScopeToQuery(EloquentBuilder $query, string $column, mixed $value): EloquentBuilder
    {
        if ($this->isDate()) {
            return $query->whereDate($column, $value);
        }

        return $query->where($column, $value);
    }

    public function getRelationship(Model $record, ?string $name = null): ?Relation
    {
        if (blank($name) && (! str($this->getColumn())->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (explode('.', $name ?? $this->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    public function getRelationshipAttribute(?string $name = null): string
    {
        $name ??= $this->getColumn();

        if (! str($name)->contains('.')) {
            return $name;
        }

        return (string) str($name)->afterLast('.');
    }

    public function getRelationshipName(?string $name = null): ?string
    {
        $name ??= $this->getColumn();

        if (! str($name)->contains('.')) {
            return null;
        }

        return (string) str($name)->beforeLast('.');
    }

    public function isDate(): bool
    {
        return $this->isDate;
    }

    public function applyEagerLoading(EloquentBuilder $query): EloquentBuilder
    {
        if (! $this->getRelationship($query->getModel())) {
            return $query;
        }

        $relationshipName = $this->getRelationshipName();

        if (array_key_exists($relationshipName, $query->getEagerLoads())) {
            return $query;
        }

        return $query->with([$relationshipName]);
    }
}
