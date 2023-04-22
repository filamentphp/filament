<?php

namespace Filament\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use ReflectionParameter;

class Action extends MountableAction implements Contracts\Groupable, Contracts\HasRecord, Contracts\SubmitsForm
{
    use Concerns\BelongsToLivewire;
    use Concerns\CanSubmitForm;
    use Concerns\HasMountableArguments;
    use Concerns\InteractsWithRecord;

    public function getLivewireCallActionName(): string
    {
        return 'callMountedAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if (! $this->isMountedOnClick()) {
            return null;
        }

        if ($this->getUrl()) {
            return null;
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

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
