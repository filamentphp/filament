<?php

namespace Filament\Infolists\Components;

use Filament\Infolists\Concerns\HasColumns;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use ReflectionParameter;

class Component extends ViewComponent
{
    use Concerns\BelongsToContainer;
    use Concerns\CanBeHidden;
    use Concerns\CanGrow;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\HasChildComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasId;
    use Concerns\HasInlineLabel;
    use Concerns\HasLabel;
    use Concerns\HasMaxWidth;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use HasColumns;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'component';

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'record' => $this->getRecord(),
            'state' => $this->getState(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
