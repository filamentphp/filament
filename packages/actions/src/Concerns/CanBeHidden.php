<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\ActionGroup;
use Filament\Support\ArrayRecord;
use Illuminate\Database\Eloquent\Model;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    protected ?bool $cachedIsAuthorized = null;

    protected ?bool $cachedIsHidden = null;

    protected ?bool $cachedIsHiddenInGroup = null;

    protected ?bool $cachedIsAuthorizedOrNotHiddenWhenUnauthorized = null;

    protected ?string $cachedVisibilityRecordKey = null;

    protected bool $hasVisibilityCache = false;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        if (! $this->hasTable()) {
            return $this->getGroup()?->baseIsHidden()
                ? true
                : $this->isHiddenInGroup();
        }

        if (! $this->prepareVisibilityCache()) {
            return $this->getGroup()?->baseIsHidden()
                ? true
                : $this->isHiddenInGroup();
        }

        return $this->cachedIsHidden ??= ($this->getGroup()?->baseIsHidden()
            ? true
            : $this->isHiddenInGroup());
    }

    public function isHiddenInGroup(): bool
    {
        if (! $this->hasTable()) {
            return $this->resolveIsHiddenInGroup();
        }

        if (! $this->prepareVisibilityCache()) {
            return $this->resolveIsHiddenInGroup();
        }

        return $this->cachedIsHiddenInGroup ??= $this->resolveIsHiddenInGroup();
    }

    protected function resolveIsHiddenInGroup(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        if (! $this->evaluate($this->isVisible)) {
            return true;
        }

        if ($this instanceof ActionGroup) {
            foreach ($this->getActions() as $action) {
                if (! $action->isHiddenInGroup()) {
                    return false;
                }
            }

            return true;
        }

        return ! $this->isAuthorizedOrNotHiddenWhenUnauthorized();
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }

    protected function prepareVisibilityCache(): bool
    {
        $current = $this->getCurrentVisibilityRecordKey();

        if ($current === null) {
            $this->clearVisibilityCache();

            return false;
        }

        if ($this->hasVisibilityCache && ($this->cachedVisibilityRecordKey === $current)) {
            return true;
        }

        $this->clearVisibilityCache();
        $this->cachedVisibilityRecordKey = $current;
        $this->hasVisibilityCache = true;

        return true;
    }

    protected function getCurrentVisibilityRecordKey(): ?string
    {
        $record = $this->getRecord();

        if ($record instanceof Model) {
            return (string) spl_object_id($record);
        }

        if (is_array($record)) {
            $key = $record[ArrayRecord::getKeyName()] ?? null;

            return ($key === null) ? null : (string) $key;
        }

        return '__null__';
    }

    public function clearVisibilityCache(): void
    {
        $this->cachedIsAuthorized = null;
        $this->cachedIsHidden = null;
        $this->cachedIsHiddenInGroup = null;
        $this->cachedIsAuthorizedOrNotHiddenWhenUnauthorized = null;
        $this->cachedVisibilityRecordKey = null;
        $this->hasVisibilityCache = false;
    }
}
