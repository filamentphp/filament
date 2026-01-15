<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public ?array $disabledNotification = null;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function disabledNotification(string | Closure | null $title = null, string | Closure | null $icon = null, string | Closure | null $color = null, string | Closure | null $duration = null, string | Closure | null $body = null): static
    {
        if (! empty($title)) {
            $this->disabledNotification = compact('title', 'icon', 'color', 'duration', 'body');
        }

        return $this;
    }

    /**
     * @return array<string | Closure | null> | null
     */
    public function getDisabledNotification(): ?array
    {
        return $this->evaluate($this->disabledNotification);
    }

    public function isDisabled(): bool
    {
        if ($this->evaluate($this->isDisabled)) {
            return true;
        }

        if ($this->isHidden()) {
            return true;
        }

        if ($this->hasAuthorizationNotification()) {
            return false;
        }

        return ! $this->isAuthorized();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
