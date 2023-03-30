<?php

namespace Filament\Actions;

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

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'record' => $this->getRecord(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
