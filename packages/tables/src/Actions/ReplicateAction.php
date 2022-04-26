<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ReplicateAction extends Action
{
    protected array | Closure | null $exclude = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    protected ?Closure $afterReplicaSavedCallback = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.actions.replicate.label'));

        $this->modalButton(fn (ReplicateAction $action): string => $action->getLabel());

        $this->action(function (ReplicateAction $action, Model $record, array $data = []) {
            $replica = $record->replicate($action->getExcludedAttributes());

            $action->callBeforeReplicaSaved($replica, $data);

            $replica->save();

            Filament::notify('success', __('tables::table.actions.replicate.messages.replicated'));

            return $action->callAfterReplicaSaved($replica, $data);
        });
    }

    public function exclude(array | Closure $attributes): static
    {
        $this->exclude = $attributes;

        return $this;
    }

    public function without(array | Closure $attributes): static
    {
        return $this->exclude($attributes);
    }

    public function beforeReplicaSaved(Closure $callback): static
    {
        $this->beforeReplicaSavedCallback = $callback;

        return $this;
    }

    public function callBeforeReplicaSaved(Model $replica, array $data = []): void
    {
        $this->evaluate($this->beforeReplicaSavedCallback, [
            'replica' => $replica,
            'data' => $data,
        ]);
    }

    public function afterReplicaSaved(Closure $callback): static
    {
        $this->afterReplicaSavedCallback = $callback;

        return $this;
    }

    public function callAfterReplicaSaved(Model $replica, array $data = [])
    {
        return $this->evaluate($this->afterReplicaSavedCallback, [
            'replica' => $replica,
            'data' => $data,
        ]);
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->exclude);
    }
}
