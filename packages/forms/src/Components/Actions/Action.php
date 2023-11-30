<?php

namespace Filament\Forms\Components\Actions;

use Exception;
use Filament\Actions\Concerns\HasMountableArguments;
use Filament\Actions\MountableAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

class Action extends MountableAction
{
    use Concerns\BelongsToComponent;
    use HasMountableArguments;

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedFormComponentAction';
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        if ($event = $this->getLivewireEventClickHandler()) {
            return $event;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
            $argumentsParameter .= '';
        }

        $componentKey = $this->getComponent()->getKey();

        if (blank($componentKey)) {
            $componentClass = $this->getComponent()::class;

            throw new Exception("The form component [{$componentClass}] must have a [key()] set in order to use actions. This [key()] must be a unique identifier for the component.");
        }

        return "mountFormComponentAction('{$componentKey}', '{$this->getName()}'{$argumentsParameter})";
    }

    public function toFormComponent(): ActionContainer
    {
        $component = ActionContainer::make($this);

        $this->component($component);

        return $component;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'component' => [$this->getComponent()],
            'context', 'operation' => [$this->getComponent()->getContainer()->getOperation()],
            'get' => [$this->getComponent()->getGetCallback()],
            'model' => [$this->getComponent()->getModel()],
            'record' => [$this->getComponent()->getRecord()],
            'set' => [$this->getComponent()->getSetCallback()],
            'state' => [$this->getComponent()->getState()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getComponent()->getRecord();

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function getInfolistName(): string
    {
        return 'mountedFormComponentActionInfolist';
    }
}
