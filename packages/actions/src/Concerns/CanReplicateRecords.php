<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait CanReplicateRecords
{
    use CanCustomizeProcess;

    protected ?Closure $beforeReplicaSaved = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $excludedAttributes = null;

    protected ?Model $replica = null;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'replicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::replicate.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::replicate.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::replicate.single.modal.actions.replicate.label'));

        $this->successNotificationTitle(__('filament-actions::replicate.single.notifications.replicated.title'));

        $this->fillForm(function (Model $record): array {
            $data = $record->attributesToArray();

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function () {
            $result = $this->process(function (Model $record) {
                $this->replica = $record->replicate($this->getExcludedAttributes());

                $this->callBeforeReplicaSaved();

                $this->replica->save();
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
        $this->beforeReplicaSaved = $callback;

        return $this;
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `after()` instead.
     */
    public function afterReplicaSaved(Closure $callback): static
    {
        $this->after($callback);

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

    public function callBeforeReplicaSaved(): void
    {
        $this->evaluate($this->beforeReplicaSaved);
    }

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->excludedAttributes);
    }

    public function getReplica(): ?Model
    {
        return $this->replica;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'replica' => [$this->getReplica()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
