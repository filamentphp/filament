<?php

namespace Filament\Schemas\Concerns;

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
        if ($parentComponent = $this->getParentComponent()) {
            return $parentComponent->getChildSchemaRootHeadingLevel();
        }

        return $this->rootHeadingLevel;
    }
}
