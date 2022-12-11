<?php

namespace Filament\Tables\Grouping;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Tables\Concerns\HasNestedRelationships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Group
{
    use HasNestedRelationships;
    use EvaluatesClosures;

    protected ?string $column;

    protected ?Closure $getDescriptionUsing = null;

    protected ?Closure $getTitleFromRecordUsing = null;

    protected ?Closure $orderQueryUsing = null;

    protected ?Closure $scopeQueryUsing = null;

    protected ?string $label;

    protected string $id;

    protected bool $isCollapsible = false;

    final public function __construct(string $id = null)
    {
        $this->id($id);
    }

    public static function make(string $id = null): static
    {
        return app(static::class, ['id' => $id]);
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

    public function getDescriptionUsing(?Closure $callback): static
    {
        $this->getDescriptionUsing = $callback;

        return $this;
    }

    public function getTitleFromRecordUsing(?Closure $callback): static
    {
        $this->getTitleFromRecordUsing = $callback;

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

    public function isCollapsible(): bool
    {
        return $this->isCollapsible;
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

    public function getDescription(Model $record, string $title): ?string
    {
        if (! $this->getDescriptionUsing) {
            return null;
        }

        return app()->call($this->getDescriptionUsing, [
            'record' => $record,
            'title' => $title,
        ]);
    }

    public function getTitle(Model $record): string
    {
        $column = $this->getColumn();

        if ($this->getTitleFromRecordUsing) {
            return app()->call($this->getTitleFromRecordUsing, [
                'column' => $column,
                'record' => $record,
            ]);
        }

        // $$$ hugh
        return $this->getNestedAttribute($column, $record);
    }

    public function orderQuery(Builder $query, string $direction): Builder
    {
        $column = $this->getColumn();

        if ($this->orderQueryUsing) {
            return app()->call($this->orderQueryUsing, [
                'column' => $column,
                'direction' => $direction,
                'query' => $query,
            ]) ?? $query;
        }

        // $$$ hugh
        return $this->getNestedOrderBy($query, $column, $direction);
    }

    public function scopeQuery(Builder $query, Model $record): Builder
    {
        $column = $this->getColumn();

        if ($this->scopeQueryUsing) {
            return app()->call($this->scopeQueryUsing, [
                'column' => $column,
                'query' => $query,
                'record' => $record,
            ]) ?? $query;
        }

        // $$$ hugh
        return $this->getNestedQuery(
            $query,
            $column,
            $record,
            fn (Builder $query) => $query->where(
                $this->getRelationshipAttribute($column),
                '=',
                $this->getNestedAttribute($column, $record)
            )
        );
    }
}
