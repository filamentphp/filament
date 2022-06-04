<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\Model;

trait CanReplicateRecords
{
    protected ?Closure $afterReplicaSavedCallback = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    protected array | Closure | null $excludedAttributes = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.actions.replicate.label'));

        $this->modalButton(static fn (ReplicateAction $action): string => $action->getLabel());

        $this->successNotification(__('tables::table.actions.replicate.messages.replicated'));

        $this->icon('heroicon-s-duplicate');

        $this->mountUsing(static function (ReplicateAction $action, Model $record, ?ComponentContainer $form = null): void {
            if (! $action->shouldOpenModal()) {
                return;
            }

            if (! $form) {
                return;
            }

            $form->fill($record->toArray());
        });

        $this->action(static function (ReplicateAction $action, Model $record, array $data = []) {
            $replica = $record->replicate($action->getExcludedAttributes());

            $action->callBeforeReplicaSaved($replica, $data);

            $replica->save();

            $action->sendSuccessNotification();

            return $action->callAfterReplicaSaved($replica, $data);
        });
    }

    public function beforeReplicaSaved(Closure $callback): static
    {
        $this->beforeReplicaSavedCallback = $callback;

        return $this;
    }

    public function afterReplicaSaved(Closure $callback): static
    {
        $this->afterReplicaSavedCallback = $callback;

        return $this;
    }

    public function excludeAttributes(array | Closure | null $attributes): static
    {
        $this->excludedAttributes = $attributes;

        return $this;
    }

    public function callBeforeReplicaSaved(Model $replica, array $data = []): void
    {
        $this->evaluate($this->beforeReplicaSavedCallback, [
            'data' => $data,
            'replica' => $replica,
        ]);
    }

    public function callAfterReplicaSaved(Model $replica, array $data = [])
    {
        return $this->evaluate($this->afterReplicaSavedCallback, [
            'data' => $data,
            'replica' => $replica,
        ]);
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->excludedAttributes);
    }
}
