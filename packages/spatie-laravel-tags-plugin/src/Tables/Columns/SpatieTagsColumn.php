<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SpatieTagsColumn extends TextColumn
{
    protected string | AllTagTypes | Closure | null $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type(AllTagTypes::make());

        $this->badge();
    }

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        return $this->cacheState(function (): array {
            $state = parent::getState();

            if ($state && (! $state instanceof Collection) && (! is_array($state))) {
                return $state;
            }

            $record = $this->getRecord();

            if ($this->hasRelationship($record)) {
                $record = $this->getRelationshipResults($record);
            }

            $records = Arr::wrap($record);

            $state = [];

            foreach ($records as $record) {
                /** @var Model $record */
                if (! (method_exists($record, 'tags') && method_exists($record, 'tagsWithType'))) {
                    continue;
                }

                $type = $this->getType();

                if ($this->isAnyTagTypeAllowed()) {
                    $tags = $record->getRelationValue('tags');
                } else {
                    $tags = $record->tagsWithType($type);
                }

                $state = [
                    ...$state,
                    ...$tags->pluck('name')->all(),
                ];
            }

            return array_unique($state);
        });
    }

    public function type(string | AllTagTypes | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string | AllTagTypes | null
    {
        return $this->evaluate($this->type);
    }

    public function isAnyTagTypeAllowed(): bool
    {
        return $this->getType() instanceof AllTagTypes;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>|Relation  $query
     * @return Builder<TModel>|Relation
     */
    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->hasRelationship($query->getModel())) {
            return $query->with(["{$this->getRelationshipName($query->getModel())}.tags"]);
        }

        return $query->with(['tags']);
    }
}
