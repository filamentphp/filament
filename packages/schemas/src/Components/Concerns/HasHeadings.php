<?php

namespace Filament\Schemas\Components\Concerns;

trait HasHeadings
{
    public function getHeadingLevel(int $index = 0): int
    {
        return $this->getContainer()->getRootHeadingLevel() + $index;
    }

    public function getHeadingTag(int $index = 0): string
    {
        $level = $this->getHeadingLevel($index);

        if ($level > 6) {
            return 'p';
        }

        return "h{$level}";
    }

    public function getChildSchemaRootHeadingLevel(): int
    {
        return $this->getHeadingLevel() + $this->getHeadingsCount();
    }

    public function getHeadingsCount(): int
    {
        return 0;
    }
}
