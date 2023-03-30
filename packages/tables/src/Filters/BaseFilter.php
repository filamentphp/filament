<?php

namespace Filament\Tables\Filters;

use Exception;
use Filament\Support\Components\Component;
use Illuminate\Support\Traits\Conditionable;
use ReflectionParameter;

class BaseFilter extends Component
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\CanResetState;
    use Concerns\HasColumns;
    use Concerns\HasDefaultState;
    use Concerns\HasFormSchema;
    use Concerns\HasIndicators;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;

    protected string $evaluationIdentifier = 'filter';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $filterClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Filter of class [$filterClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($filterClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'livewire' => $this->getLivewire(),
            'table' => $this->getTable(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
