<?php

namespace Filament\Actions;

use Exception;
use Filament\Schema\Components\Actions\ActionContainer;
use Filament\Schema\Components\Actions\ActionContainer as InfolistActionContainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;

class Action extends MountableAction implements Contracts\Groupable, Contracts\HasRecord
{
    use Concerns\BelongsToComponent;
    use Concerns\CanSubmitForm;
    use Concerns\HasMountableArguments;
    use Concerns\InteractsWithRecord;

    public function getLivewireCallMountedActionName(): string
    {
        return $this->getComponent() ?
            'callMountedFormComponentAction' :
            'callMountedAction';
    }

    public function getLivewire(): object
    {
        return $this->getComponent()?->getLivewire() ?? parent::getLivewire();
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

        $action = [
            'name' => $this->getName(),
        ];

        if (count($arguments = $this->getArguments())) {
            $action['arguments'] = $arguments;
        }

        $component = $this->getComponent();

        if (! $component) {
            $argumentsParameter = '';

            if (count($arguments)) {
                $argumentsParameter .= ', ';
                $argumentsParameter .= Js::from($arguments);
            }

            return "mountAction('{$this->getName()}'{$argumentsParameter})";
        }

        $componentKey = $component->getKey();

        if (blank($componentKey)) {
            $componentClass = $this->getComponent()::class;

            throw new Exception("The schema component [{$componentClass}] must have a [key()] set in order to use actions. This [key()] must be a unique identifier for the component.");
        }

        $action['context']['schemaComponent'] = $componentKey;

        return 'mountFormComponentAction(' . Js::from($action) . ')';
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'component' => [$this->getComponent()],
            'context', 'operation' => [$this->getComponent()->getContainer()->getOperation()],
            'get' => [$this->getComponent()->makeGetUtility()],
            'model' => [$this->getModel() ?? $this->getComponent()?->getModel()],
            'record' => [$this->getRecord() ?? $this->getComponent()?->getRecord()],
            'set' => [$this->getComponent()->makeSetUtility()],
            'state' => [$this->getComponent()->getState()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord() ?? $this->getComponent()?->getRecord();

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function shouldClearRecordAfter(): bool
    {
        return ! $this->getRecord()?->exists;
    }

    public function clearRecordAfter(): void
    {
        if (! $this->shouldClearRecordAfter()) {
            return;
        }

        $this->record(null);
    }

    public function getInfolistName(): string
    {
        return $this->getComponent() ?
            'mountedFormComponentActionInfolist' :
            'mountedActionInfolist';
    }

    public function toFormComponent(): ActionContainer
    {
        $component = ActionContainer::make($this);

        $this->component($component);

        return $component;
    }

    public function toInfolistComponent(): InfolistActionContainer
    {
        $component = InfolistActionContainer::make($this);

        $this->component($component);

        return $component;
    }
}
