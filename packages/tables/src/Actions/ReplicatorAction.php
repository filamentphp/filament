<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ReplicatorAction extends ButtonAction
{
    protected bool $isButton = false;

    protected array | Closure | null $exclude = null;

    protected ?Closure $beforeSavingCallback = null;

    protected ?Closure $afterSavingCallback = null;

    protected function setUp(): void
    {
        $this->color('success');

        $this->modalButton(function (ReplicatorAction $action) {
            return $action->getLabel();
        });

        $this->action(function (ReplicatorAction $action, Model $record, array $data = []) {
            $replica = $record->replicate($action->getExcludedAttributes());

            $action->callBeforeSaving($replica, $data);

            $replica->save();

            Filament::notify('success', 'Record replicated.');

            return $action->callAfterSaving($replica, $data);
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

    public function beforeSaving(Closure $callback): static
    {
        $this->beforeSavingCallback = $callback;

        return $this;
    }

    public function callBeforeSaving(Model $replica, array $data = []): void
    {
        $this->evaluate($this->beforeSavingCallback, [
            'replica' => $replica,
            'data' => $data,
        ]);
    }

    public function afterSaving(Closure $callback): static
    {
        $this->afterSavingCallback = $callback;

        return $this;
    }

    public function callAfterSaving(Model $replica, array $data = []): mixed
    {
        return $this->evaluate($this->afterSavingCallback, [
            'replica' => $replica,
            'data' => $data,
        ]);
    }

    public function button(bool $condition = true): static
    {
        $this->isButton = $condition;

        return $this;
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->exclude);
    }

    public function getRelationships(): ?array
    {
        return $this->evaluate($this->relationships);
    }

    public function getView(): string
    {
        if ($this->isButton) {
            return 'tables::actions.button-action';
        }

        return 'tables::actions.link-action';
    }
}
