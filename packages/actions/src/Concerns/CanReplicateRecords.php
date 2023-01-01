<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;

trait CanReplicateRecords
{
    use CanCustomizeProcess;

    protected ?Closure $afterReplicaSavedCallback = null;

    protected ?Closure $beforeReplicaSavedCallback = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $excludedAttributes = null;

    public static function getDefaultName(): ?string
    {
        return 'replicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::replicate.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::replicate.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-actions::replicate.single.modal.actions.replicate.label'));

        $this->successNotificationTitle(__('filament-actions::replicate.single.messages.replicated'));

        $this->fillForm(fn (Model $record): array => $record->attributesToArray());

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

    /**
     * @param  array<string> | Closure | null  $attributes
     */
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

    public function callAfterReplicaSaved(Model $replica): mixed
    {
        return $this->evaluate($this->afterReplicaSavedCallback, [
            'replica' => $replica,
        ]);
    }

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->excludedAttributes);
    }
}
