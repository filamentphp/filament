<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class ReplicatorAction extends Action
{
    protected string $type = 'button';

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

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function button(): static
    {
        return $this->type('button');
    }

    public function link(): static
    {
        return $this->type('link');
    }

    public function iconButton(): static
    {
        return $this->type('iconButton');
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->exclude);
    }

    public function getView(): string
    {
        return match ($this->type) {
            'button' => 'tables::actions.button-action',
            'iconButton' => 'tables::actions.icon-button-action',
            'link' => 'tables::actions.link-action'
        };
    }
}
