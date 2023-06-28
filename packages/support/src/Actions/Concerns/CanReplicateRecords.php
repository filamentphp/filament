<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;

trait CanReplicateRecords
{
    use CanCustomizeProcess;

    protected ?Closure $afterReplicaSavedCallback = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    protected array | Closure | null $excludedAttributes = null;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'replicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/replicate.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/replicate.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/replicate.single.modal.actions.replicate.label'));

        $this->successNotificationTitle(__('filament-support::actions/replicate.single.messages.replicated'));

        $this->mountUsing(function (Model $record, ?ComponentContainer $form = null): void {
            if (! $this->hasForm()) {
                return;
            }

            if (! $form) {
                return;
            }

            $data = $record->attributesToArray();

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            $form->fill($data);
        });

        $this->action(function () {
            $result = $this->process(function (Model $record) {
                $replica = $record->replicate($this->getExcludedAttributes());

                $this->callBeforeReplicaSaved($replica);

                $replica->save();

                return $this->callAfterReplicaSaved($replica);
            });

            try {
                return $result;
            } finally {
                $this->success();
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

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
