<?php

namespace Filament\Tables\Grouping;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Group
{
    protected ?string $column;

    protected ?string $label;

    protected string $id;

    final public function __construct(string $id = null)
    {
        $this->id($id);
    }

    public static function make(string $id = null): static
    {
        return app(static::class, ['id' => $id]);
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

    public function getTitleFromRecord(Model $record): string
    {
        return $record->getAttribute($this->getColumn());
    }

    public function orderQuery(Builder $query): Builder
    {
        return $query->orderBy($this->getColumn());
    }

    public function scopeQuery(Builder $query, Model $record): Builder
    {
        $column = $this->getColumn();

        return $query->where($column, $record->getAttribute($column));
    }
}
