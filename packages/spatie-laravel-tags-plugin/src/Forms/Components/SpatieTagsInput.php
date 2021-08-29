<?php

namespace Filament\Forms\Components;

use Illuminate\Database\Eloquent\Model;

class SpatieTagsInput extends TagsInput
{
    protected $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (SpatieTagsInput $component, ?Model $model): void {
            if (! $model) {
                $component->state([]);

                return;
            }

            if ($type = $component->getType()) {
                $tags = $model->tagsWithType($type);
            } else {
                $tags = $model->tags;
            }

            $component->state($tags->pluck('name'));
        });

        $this->dehydrated(false);
    }

    public function saveRelationships(): void
    {
        $model = $this->getModel();
        $tags = $this->getState();

        if ($type = $this->getType()) {
            $model->syncTagsWithType($tags, $type);

            return;
        }

        $model->syncTags($tags);
    }

    public function type(string | callable $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->evaluate($this->type);
    }
}
