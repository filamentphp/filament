<?php

namespace Filament\Tables\Table\Concerns;

trait HasHeadings
{
    protected int $rootHeadingLevel = 2;

    public function rootHeadingLevel(int $level): static
    {
        $this->rootHeadingLevel = $level;

        return $this;
    }

    public function getRootHeadingLevel(): int
    {
        return $this->rootHeadingLevel;
    }

    public function getHeadingLevel(int $index = 0): int
    {
        return $this->getRootHeadingLevel() + $index;
    }

    public function getHeadingTag(int $index = 0): string
    {
        $level = $this->getHeadingLevel($index);

        if ($level > 6) {
            return 'p';
        }

        return "h{$level}";
    }
}
