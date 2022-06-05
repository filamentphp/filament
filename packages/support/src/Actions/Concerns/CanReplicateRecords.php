<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Action;
use Filament\Support\Actions\Contracts\HasRecord;
use Filament\Support\Actions\Contracts\ReplicatesRecords;
use Illuminate\Database\Eloquent\Model;

trait CanReplicateRecords
{
    protected ?Closure $afterReplicaSavedCallback = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    protected array | Closure | null $excludedAttributes = null;

    public static function make(string $name = 'replicate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/replicate.single.label.default'));

        $this->modalHeading(fn (Action | HasRecord $action): string => __('filament-support::actions/replicate.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/replicate.single.modal.actions.replicate.label'));

        $this->successNotificationMessage(__('filament-support::actions/replicate.single.messages.replicated'));

        $this->icon('heroicon-s-duplicate');

        $this->mountUsing(static function (Action $action, Model $record, ?ComponentContainer $form = null): void {
            if (! $action->hasForm()) {
                return;
            }

            if (! $form) {
                return;
            }

            $form->fill($record->toArray());
        });

        $this->action(static function (Action | ReplicatesRecords $action, Model $record) {
            $replica = $record->replicate($action->getExcludedAttributes());

            $action->callBeforeReplicaSaved($replica);

            $replica->save();

            try {
                return $action->callAfterReplicaSaved($replica);
            } finally {
                $action->success();
            }
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

    public function callBeforeReplicaSaved(Model $replica): void
    {
        $this->evaluate($this->beforeReplicaSavedCallback, [
            'replica' => $replica,
        ]);
    }

    public function callAfterReplicaSaved(Model $replica)
    {
        return $this->evaluate($this->afterReplicaSavedCallback, [
            'replica' => $replica,
        ]);
    }

    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->excludedAttributes);
    }
}
