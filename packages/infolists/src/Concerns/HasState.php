<?php

namespace Filament\Infolists\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait HasState
{
    protected ?string $statePath = null;

    public ?Model $record = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $state = null;

    protected string $cachedFullStatePath;

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function state(?array $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function record(?Model $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        if (isset($this->cachedFullStatePath)) {
            return $this->cachedFullStatePath;
        }

        $pathComponents = [];

        if ($isAbsolute && $parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (($statePath = $this->statePath) !== null) {
            $pathComponents[] = $statePath;
        }

        return $this->cachedFullStatePath = implode('.', $pathComponents);
    }

    /**
     * @return Model | array<string, mixed>
     */
    public function getState(): Model | array
    {
        $state = $this->state ?? $this->getParentComponent()?->getContainer()->getState();

        if ($state !== null) {
            return $state;
        }

        $record = $this->getRecord();

        if (! $record) {
            throw new Exception('Infolist has no [record()] or [state()] set.');
        }

        return $record;
    }

    public function getRecord(): ?Model
    {
        if ($this->record instanceof Model) {
            return $this->record;
        }

        return $this->getParentComponent()?->getRecord();
    }

    protected function flushCachedStatePath(): void
    {
        unset($this->cachedFullStatePath);
    }
}
