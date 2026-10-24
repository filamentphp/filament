<?php

namespace Filament\Schemas\Components\Concerns;

trait CanLimitItemsForProcessing
{
    protected ?int $maxItemsForProcessing = null;

    public function maxItemsForProcessing(?int $count): static
    {
        $this->maxItemsForProcessing = $count;

        return $this;
    }

    public function getMaxItemsForProcessing(): ?int
    {
        return $this->maxItemsForProcessing;
    }

    protected function ensureItemsCanBeProcessed(int $count): void
    {
        $maxItems = $this->getMaxItemsForProcessing();

        if (($maxItems !== null) && ($count > $maxItems)) {
            abort(413);
        }
    }
}
