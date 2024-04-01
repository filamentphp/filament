<?php

namespace Filament\Actions;

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
            'callMountedAction' :
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

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
        }

        $component = $this->getComponent();
        $context = [];

        if (filled($componentKey = $component?->getKey())) {
            $context['schemaComponent'] = $componentKey;
        }

        $contextParameter = '';

        if (filled($context)) {
            $contextParameter .= ', ';
            $contextParameter .= Js::from($context);

            if ($argumentsParameter === '') {
                $argumentsParameter = ', {}';
            }
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter}{$contextParameter})";
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
            'mountedActionInfolist' :
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
