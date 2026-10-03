<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait CanBeCopied
{
    protected bool | Closure $isCopyable = false;

    protected string | Closure | null $copyableState = null;

    protected string | Closure | null $copyMessage = null;

    protected int | Closure | null $copyMessageDuration = null;

    public function copyable(bool | Closure $condition = true): static
    {
        $this->isCopyable = $condition;

        return $this;
    }

    public function copyableState(string | Closure | null $state): static
    {
        $this->copyableState = $state;

        return $this;
    }

    public function copyMessage(string | Closure | null $message): static
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function copyMessageDuration(int | Closure | null $duration): static
    {
        $this->copyMessageDuration = $duration;

        return $this;
    }

    public function isCopyable(mixed $state, ?Model $relatedRecord = null): bool
    {
        return (bool) $this->evaluate($this->isCopyable, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);
    }

    public function getCopyableState(mixed $state, ?Model $relatedRecord = null): ?string
    {
        return $this->evaluate($this->copyableState, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);
    }

    public function getCopyMessage(mixed $state, ?Model $relatedRecord = null): string
    {
        return $this->evaluate($this->copyMessage, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]) ?? __('filament::components/copyable.messages.copied');
    }

    public function getCopyMessageDuration(mixed $state, ?Model $relatedRecord = null): int
    {
        return $this->evaluate($this->copyMessageDuration, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]) ?? 2000;
    }

    public function hasCopyable(): bool
    {
        return $this->isCopyable !== false;
    }
}
