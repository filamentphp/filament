<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Events\RecordChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Illuminate\Database\Eloquent\Model;

trait HasWebSocketListeners
{
    /**
     * @var array<string, Closure | null> | array<string> | Closure
     */
    protected array | Closure $webSocketListeners = [];

    protected bool | Closure $shouldReceiveRecordUpdates = false;

    protected ?Closure $afterRecordUpdateReceived = null;

    /**
     * @param  array<string, Closure | null> | array<string> | Closure  $listeners
     */
    public function webSocketListeners(array | Closure $listeners): static
    {
        $this->webSocketListeners = $listeners;

        return $this;
    }

    /**
     * @return array<string, array{callback: Closure | null, authorizationMethod?: string, refreshOnSubscription?: bool, coalesce?: bool}>
     */
    public function getWebSocketListeners(): array
    {
        $listeners = [];

        if (
            $this->shouldReceiveRecordUpdates()
            && ($record = $this->getRecord()) instanceof Model
            && $record->exists
            && filled(FilamentRealtime::getSubscription($record::class)?->getEvents())
        ) {
            $listeners['private:' . RecordChangedBroadcast::getChannel($record) . ',.filament.record-changed'] = [
                'callback' => static function (Action $action, ?Schema $schema): void {
                    $action->callAfterRecordUpdateReceived(['schema' => $schema, 'form' => $schema]);
                },
                'authorizationMethod' => 'authorizeActionRecordUpdates',
                'refreshOnSubscription' => true,
                'coalesce' => true,
            ];
        }

        foreach ($this->evaluate($this->webSocketListeners) as $name => $callback) {
            if (is_int($name)) {
                $name = $callback;
                $callback = null;
            }

            $listeners[$name]['callback'] = $callback;
        }

        return $listeners;
    }

    public function receiveRecordUpdates(bool | Closure $condition = true): static
    {
        $this->shouldReceiveRecordUpdates = $condition;

        return $this;
    }

    public function shouldReceiveRecordUpdates(): bool
    {
        return (bool) $this->evaluate($this->shouldReceiveRecordUpdates);
    }

    public function afterRecordUpdateReceived(?Closure $callback): static
    {
        $this->afterRecordUpdateReceived = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function callAfterRecordUpdateReceived(array $parameters = []): void
    {
        $this->evaluate($this->afterRecordUpdateReceived, $parameters);
    }
}
