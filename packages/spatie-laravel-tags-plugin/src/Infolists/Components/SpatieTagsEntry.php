<?php

namespace Filament\Infolists\Components;

use Illuminate\Support\Collection;

class SpatieTagsEntry extends TextEntry
{
    protected ?string $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->badge();
    }

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        $state = parent::getState();

        if ($state && (! $state instanceof Collection)) {
            return $state;
        }

        $record = $this->getRecord();

        if (! $record) {
            return [];
        }

        $relationshipName = $this->getRelationshipName();

        if (filled($relationshipName)) {
            $record = $record->getRelationValue($relationshipName);
        }

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
}
