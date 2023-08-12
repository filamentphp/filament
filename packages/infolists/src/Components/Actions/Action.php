<?php

namespace Filament\Infolists\Components\Actions;

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

        $infolistComponent = $this->getInfolistComponent();

        if (! $infolistComponent) {
            return "mountInfolistAction('{$this->getName()}')";
        }

        return "mountInfolistAction('{$this->getName()}', '{$infolistComponent->getKey()}', '{$infolistComponent->getInfolist()->getName()}')";
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
            'record' => [$this->getInfolistComponent()->getRecord()],
            'infolist' => [$this->getInfolist()],
            'infolistComponent' => [$this->getInfolistComponent()],
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

        return $action->component($this->getInfolistComponent());
    }
}
