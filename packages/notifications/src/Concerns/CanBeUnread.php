<?php

namespace Filament\Notifications\Concerns;

trait CanBeUnread
{
    protected bool $isUnread = false;

    public function unread(bool $condition = true): static
    {
        $this->isUnread = $condition;

        return $this;
    }

    public function isUnread(): bool
    {
        return $this->isUnread;
    }
}
