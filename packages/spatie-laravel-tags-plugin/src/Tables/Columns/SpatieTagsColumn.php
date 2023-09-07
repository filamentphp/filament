<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class SpatieTagsColumn extends TagsColumn
{
    protected string | Closure | AllTagTypes | null $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        //all all tag types by default:
        $this->type(new AllTagTypes());
    }

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        $state = $this->getState();

        if ($state && (! $state instanceof Collection)) {
            return $state;
        }

        $record = $this->getRecord();

        if ($this->queriesRelationships($record)) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        if (! method_exists($record, 'tagsWithType') || ! method_exists($record, 'tags')) {
            return [];
        }

        $type = $this->getType();

        $tags = $this->allowsAllTagTypes() ? $record->tags() : $record->tagsWithType($type);

        return $tags->pluck('name')->toArray();
    }

    public function type(string | Closure | AllTagTypes | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string | AllTagTypes | null
    {
        return $this->evaluate($this->type);
    }

    public function allowsAllTagTypes(): bool
    {
        return $this->getType() instanceof AllTagTypes;
    }

    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->queriesRelationships($query->getModel())) {
            return $query->with(["{$this->getRelationshipName()}.tags"]);
        }

        return $query->with(['tags']);
    }
}
