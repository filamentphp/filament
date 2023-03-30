<?php

namespace Filament\Infolists;

use Filament\Support\Components\ViewComponent;
use ReflectionParameter;

class ComponentContainer extends ViewComponent
{
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeHidden;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasInlineLabels;
    use Concerns\HasState;

    protected string $view = 'filament-infolists::component-container';

    protected string $evaluationIdentifier = 'container';

    protected string $viewIdentifier = 'container';

    public static function make(): static
    {
        return app(static::class);
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'record' => $this->getRecord(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
