<?php

namespace Filament\Forms\Components;

use Filament\Forms\Concerns\HasColumns;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;

class Component extends ViewComponent
{
    use Concerns\BelongsToContainer;
    use Concerns\BelongsToModel;
    use Concerns\CanBeConcealed;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\HasActions;
    use Concerns\HasChildComponents;
    use Concerns\HasFieldWrapper;
    use Concerns\HasId;
    use Concerns\HasInlineLabel;
    use Concerns\HasKey;
    use Concerns\HasLabel;
    use Concerns\HasMaxWidth;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use Concerns\ListensToEvents;
    use HasColumns;
    use HasExtraAttributes;
    use HasStateBindingModifiers;

    protected string $evaluationIdentifier = 'component';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'context', 'operation' => [$this->getContainer()->getOperation()],
            'get' => [$this->getGetCallback()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'record' => [$this->getRecord()],
            'set' => [$this->getSetCallback()],
            'state' => [$this->getState()],
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
}
