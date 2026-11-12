<?php

namespace Filament\Support\Events;

use BackedEnum;
use Filament\Support\Components\Component;
use Filament\Support\Enums\ModelEvent;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

use function Livewire\store;

class RecordChanged extends Component implements ShouldDispatchAfterCommit
{
    protected Model $record;

    protected BackedEnum | string | null $event = null;

    protected ?string $operationId = null;

    public function __construct(Model $record)
    {
        $this->record($record);
    }

    public static function make(Model $record): static
    {
        $static = app(static::class, ['record' => $record]);
        $static->configure();

        return $static;
    }

    public function record(Model $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function event(BackedEnum | string | null $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function getEvent(): BackedEnum | string | null
    {
        if (! is_string($this->event)) {
            return $this->event;
        }

        return ModelEvent::tryFrom($this->event) ?? $this->event;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function dispatch(): static
    {
        $event = clone $this;
        $event->record(clone $this->getRecord());

        // Capture the originating save before after-commit delivery can run
        // under a different component or outside its method call.
        $component = Livewire::current();
        $operation = $component ? store($component)->get('recordSaveOperation') : null;
        $event->operationId = ($operation && ($operation['channel'] === RecordChangedBroadcast::getChannel($this->getRecord())))
            ? $operation['id']
            : null;

        event($event);

        return $this;
    }
}
