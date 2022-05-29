<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\Tag;

class SpatieTagsInput extends TagsInput
{
    protected string | Closure | null $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadStateFromRelationshipsUsing(static function (SpatieTagsInput $component, ?Model $record): void {
            if (! method_exists($record, 'tagsWithType')) {
                return;
            }

            $type = $component->getType();
            $tags = $record->tagsWithType($type);

            $component->state($tags->pluck('name')->toArray());
        });

        $this->saveRelationshipsUsing(static function (SpatieTagsInput $component, ?Model $record, array $state) {
            if (! (method_exists($record, 'syncTagsWithType') && method_exists($record, 'syncTags'))) {
                return;
            }

            if ($type = $component->getType()) {
                $record->syncTagsWithType($state, $type);

                return;
            }

            $record->syncTags($state);
        });

        $this->dehydrated(false);
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSuggestions(): array
    {
        if ($this->suggestions !== null) {
            return parent::getSuggestions();
        }

        $type = $this->getType();

        return Tag::query()
            ->when(
                filled($type),
                fn (Builder $query) => $query->where('type', $type),
                fn (Builder $query) => $query->where('type', null),
            )
            ->pluck('name')
            ->toArray();
    }

    public function getType(): ?string
    {
        return $this->evaluate($this->type);
    }
}
