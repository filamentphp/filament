<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasDescription
{
    /**
     * @var array<array{description: string | Htmlable | Closure | null, position: string | Closure | null}>
     */
    protected array $descriptions = [];

    public function description(string | Htmlable | Closure | null $description, string | Closure | null $position = 'below'): static
    {
        $this->descriptions[] = [
            'description' => $description,
            'position' => $position,
        ];

        return $this;
    }

    public function getDescriptionAbove(): string | Htmlable | null
    {
        return $this->getDescription('above');
    }

    public function getDescriptionBelow(): string | Htmlable | null
    {
        return $this->getDescription('below');
    }

    protected function getDescription(string $position): string | Htmlable | null
    {
        foreach (array_reverse($this->descriptions) as $description) {
            $descriptionPosition = ($this->evaluate($description['position']) === 'above') ? 'above' : 'below';

            if ($descriptionPosition !== $position) {
                continue;
            }

            return $this->evaluate($description['description']);
        }

        return null;
    }

    public function hasDescription(): bool
    {
        $clearedPositions = [];

        foreach (array_reverse($this->descriptions) as $description) {
            if ($description['position'] instanceof Closure) {
                if ($description['description'] !== null) {
                    return true;
                }

                continue;
            }

            $position = ($description['position'] === 'above') ? 'above' : 'below';

            if (isset($clearedPositions[$position])) {
                continue;
            }

            if ($description['description'] !== null) {
                return true;
            }

            $clearedPositions[$position] = true;

            if (count($clearedPositions) === 2) {
                return false;
            }
        }

        return false;
    }
}
