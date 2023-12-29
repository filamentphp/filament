<?php

namespace Filament\Infolists\Components\Actions;

use Exception;
use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Illuminate\Database\Eloquent\Model;

class Action extends MountableAction
{
    use Concerns\BelongsToInfolist;

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedInfolistAction';
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

        $component = $this->getComponent();

        if (! $component) {
            return "mountInfolistAction('{$this->getName()}')";
        }

        $componentKey = $component->getKey();

        if (blank($componentKey)) {
            $componentClass = $this->getComponent()::class;

            throw new Exception("The infolist component [{$componentClass}] must have a [key()] set in order to use actions. This [key()] must be a unique identifier for the component.");
        }

        return "mountInfolistAction('{$this->getName()}', '{$componentKey}', '{$component->getInfolist()->getName()}')";
    }

    public function toInfolistComponent(): ActionContainer
    {
        return ActionContainer::make($this);
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'component', 'infolistComponent' => [$this->getComponent()],
            'record' => [$this->getComponent()->getRecord()],
            'infolist' => [$this->getInfolist()],
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

    public function prepareModalAction(StaticAction $action): StaticAction
    {
        $action = parent::prepareModalAction($action);

        if (! $action instanceof Action) {
            return $action;
        }

        return $action->component($this->getComponent());
    }

    public function getInfolistName(): string
    {
        return 'mountedInfolistActionsInfolist';
    }
}
