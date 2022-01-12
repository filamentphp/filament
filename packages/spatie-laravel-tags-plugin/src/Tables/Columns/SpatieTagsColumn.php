<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;

class SpatieTagsColumn extends TagsColumn
{
    protected ?string $type = null;

    public function getTags(): array
    {
        $record = $this->getRecord();

        if (! method_exists($record, 'tagsWithType')) {
            return [];
        }

        $type = $this->getType();
        $tags = $record->tagsWithType($type);

        return $tags->pluck('name')->toArray();
    }

    public function type(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function applyEagerLoading(Builder $query): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        return $query->with(['tags']);
    }
}
