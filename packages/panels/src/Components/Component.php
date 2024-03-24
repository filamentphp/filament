<?php

namespace Filament\Components;

use Filament\Components\Concerns\HasColumns;
use Filament\Components\Concerns\HasStateBindingModifiers;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;

class Component extends ViewComponent
{
    use CanGrow;
    use \Filament\Components\Concerns\BelongsToContainer;
    use \Filament\Components\Concerns\BelongsToModel;
    use \Filament\Components\Concerns\CanBeConcealed;
    use \Filament\Components\Concerns\CanBeDisabled;
    use \Filament\Components\Concerns\CanBeHidden;
    use \Filament\Components\Concerns\CanBeRepeated;
    use \Filament\Components\Concerns\CanSpanColumns;
    use \Filament\Components\Concerns\Cloneable;
    use \Filament\Components\Concerns\HasActions;
    use \Filament\Components\Concerns\HasChildComponents;
    use \Filament\Components\Concerns\HasFieldWrapper;
    use \Filament\Components\Concerns\HasId;
    use \Filament\Components\Concerns\HasInlineLabel;
    use \Filament\Components\Concerns\HasKey;
    use \Filament\Components\Concerns\HasLabel;
    use \Filament\Components\Concerns\HasMaxWidth;
    use \Filament\Components\Concerns\HasMeta;
    use \Filament\Components\Concerns\HasState;
    use \Filament\Components\Concerns\ListensToEvents;
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
