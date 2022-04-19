<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ReplicatorAction extends Action
{
    protected array | Closure | null $exclude = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    protected ?Closure $afterReplicaSavedCallback = null;

    protected function setUp(): void
    {
        $this->color('success');

        $this->modalButton(function (ReplicatorAction $action) {
            return $action->getLabel();
        });

        $this->action(function (ReplicatorAction $action, Model $record, array $data = []) {
            $replica = $record->replicate($action->getExcludedAttributes());

            $action->callBeforeReplicaSaved($replica, $data);

            $replica->save();

            Filament::notify('success', 'Record replicated.');

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

    public function callAfterReplicaSaved(Model $replica, array $data = []): mixed
    {
        return $this->evaluate($this->afterReplicaSavedCallback, [
            'replica' => $replica,
            'data' => $data,
        ]);
    }

    public function button(): static
    {
        $this->view = 'tables::actions.button-action';

        return $this;
    }

    public function link(): static
    {
        $this->view = 'tables::actions.link-action';

        return $this;
    }

    public function iconButton(): static
    {
        $this->view = 'tables::actions.icon-button-action';

        return $this;
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->exclude);
    }
}
