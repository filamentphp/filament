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

        $this->afterStateHydrated(function (SpatieTagsInput $component, Model|string|null $model): void {
            if (! $model || is_string($model)) {
                $component->state([]);

                return;
            }

            if (! method_exists($model, 'tagsWithType')) {
                return;
            }

            $type = $component->getType();
            $tags = $model->tagsWithType($type);

            $component->state($tags->pluck('name'));
        });

        $this->dehydrated(false);
    }

    public function saveRelationships(): void
    {
        if ($this->saveRelationshipsUsing) {
            parent::saveRelationships();

            return;
        }

        $model = $this->getModel();
        $tags = $this->getState();

        if (! (method_exists($model, 'syncTagsWithType') && method_exists($model, 'syncTags'))) {
            return;
        }

        if ($type = $this->getType()) {
            $model->syncTagsWithType($tags, $type);

            return;
        }

        $model->syncTags($tags);
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
