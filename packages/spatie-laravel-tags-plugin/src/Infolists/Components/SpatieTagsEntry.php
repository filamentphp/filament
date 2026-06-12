<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class SpatieTagsEntry extends TextEntry
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
        $state = parent::getState();

        if ($state && (! $state instanceof Collection) && (! is_array($state))) {
            return $state;
        }

        $record = $this->getRecord();

        if (! $record) {
            return [];
        }

        if ($this->hasStateRelationship($record)) {
            $record = $this->getStateRelationshipResults($record);
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
}
