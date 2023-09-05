<?php

namespace Filament\Infolists;

use Filament\Support\Components\ViewComponent;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ComponentContainer extends ViewComponent
{
    use Concerns\BelongsToLivewire;
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

    final public function __construct(?Component $livewire = null)
    {
        $this->livewire($livewire);
    }

    public static function make(?Component $livewire = null): static
    {
        $static = app(static::class, ['livewire' => $livewire]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
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
