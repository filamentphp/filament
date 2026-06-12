<?php

namespace Filament\Tables\Filters;

use Filament\Support\Components\Component;
use LogicException;

class BaseFilter extends Component
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden;
    use Concerns\CanResetState;
    use Concerns\CanSpanColumns;
    use Concerns\HasColumns;
    use Concerns\HasDefaultState;
    use Concerns\HasIndicators;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasSchema;
    use Concerns\InteractsWithTableQuery;

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
            throw new LogicException("Filter of class [$filterClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($filterClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getActiveCount(): int
    {
        return count($this->getIndicators());
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
