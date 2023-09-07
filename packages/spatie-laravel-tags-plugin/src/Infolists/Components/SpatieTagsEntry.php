<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Support\Collection;

class SpatieTagsEntry extends TextEntry
{
    protected string | Closure | AllTagTypes | null $type;

    protected function setUp(): void
    {
        parent::setUp();

        //all all tag types by default:
        $this->type(new AllTagTypes());

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

        if (! method_exists($record, 'tagsWithType') || ! method_exists($record, 'tags')) {
            return [];
        }

        $type = $this->getType();

        if($this->allowsAllTagTypes()) {
            $tags = $record->tags();
        }
        else {
            $tags = $record->tagsWithType($type);
        }

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
}
