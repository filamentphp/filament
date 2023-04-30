<?php

namespace Filament\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Livewire\Component;

class Action extends MountableAction implements Contracts\Groupable, Contracts\HasRecord
{
    use Concerns\CanSubmitForm;
    use Concerns\HasMountableArguments;
    use Concerns\InteractsWithRecord;

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedAction';
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter})";
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function getLivewire(): Component
    {
        return $this->livewire;
    }
}
