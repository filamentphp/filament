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

        $this->afterStateHydrated(function (SpatieTagsInput $component, ?Model $record): void {
            if (! $record) {
                $component->state([]);

                return;
            }

            if (! method_exists($record, 'tagsWithType')) {
                return;
            }

            $type = $component->getType();
            $tags = $record->tagsWithType($type);

            $component->state($tags->pluck('name'));
        });

        $this->saveRelationshipsUsing(function (SpatieTagsInput $component, array $state) {
            $model = $component->getModel();

            if (! (method_exists($model, 'syncTagsWithType') && method_exists($model, 'syncTags'))) {
                return;
            }

            if ($type = $component->getType()) {
                $model->syncTagsWithType($state, $type);

                return;
            }

            $model->syncTags($state);
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
                fn (Builder $query) => $query->whereNull('type'),
            )
            ->pluck('name')
            ->toArray();
    }

    public function getType(): ?string
    {
        return $this->evaluate($this->type);
    }
}
