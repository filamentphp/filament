<?php

namespace Filament\Tables\Columns;

class SpatieTagsColumn extends TagsColumn
{
    protected ?string $type = null;

    public function getTags(): array
    {
        $type = $this->getType();
        $tags = $this->getRecord()->tagsWithType($type);

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
}
