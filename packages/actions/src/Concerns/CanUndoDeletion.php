<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

trait CanUndoDeletion
{
    protected bool | Closure $isUndoable = false;

    protected int | Closure | null $undoableSeconds = null;

    /**
     * @var array<class-string<Model>, array<int | string>>
     */
    protected array $undoableRecordKeys = [];

    public function undoable(bool | Closure $condition = true, int | Closure | null $seconds = null): static
    {
        $this->isUndoable = $condition;
        $this->undoableSeconds = $seconds;

        return $this;
    }

    public function isUndoable(): bool
    {
        return (bool) $this->evaluate($this->isUndoable);
    }

    public function getUndoableSeconds(): int
    {
        return $this->evaluate($this->undoableSeconds) ?? 10;
    }

    public static function getUndoDeletionCacheKey(string $token): string
    {
        return "filament-actions::undo-deletion.{$token}";
    }

    protected function rememberUndoableDeletion(Model $record): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($record))) {
            return;
        }

        $this->undoableRecordKeys[$record::class][] = $record->getKey();
    }

    protected function modifySuccessNotification(Notification $notification): void
    {
        $recordKeys = $this->undoableRecordKeys;
        $this->undoableRecordKeys = [];

        if (blank($recordKeys) || (! $this->isUndoable())) {
            return;
        }

        $livewire = $this->getLivewire();

        if (! ($livewire instanceof Component)) {
            return;
        }

        if (filled($this->evaluate($this->successRedirectUrl) ?? $this->getHasActionsLivewire()?->getDefaultActionSuccessRedirectUrl($this))) {
            return;
        }

        $seconds = $this->getUndoableSeconds();
        $token = Str::random(40);

        Cache::put(static::getUndoDeletionCacheKey($token), [
            'livewire' => $livewire->getId(),
            'user' => auth()->id(),
            'records' => $recordKeys,
            'isTable' => $this->getTable() !== null,
        ], $seconds);

        $notification
            ->seconds($seconds)
            ->actions([
                ...$notification->getActions(),
                Action::make('undo')
                    ->label(__('filament-actions::delete.undo.label'))
                    ->link()
                    ->dispatch('filament-actions::undo-deletion', [
                        'id' => $livewire->getId(),
                        'token' => $token,
                    ])
                    ->close(),
            ]);
    }
}
