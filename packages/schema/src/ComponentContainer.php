<?php

namespace Filament\Schema;

use Filament\Schema\Contracts\HasSchemas;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ComponentContainer extends ViewComponent
{
    use ComponentContainer\Concerns\BelongsToLivewire;
    use ComponentContainer\Concerns\BelongsToModel;
    use ComponentContainer\Concerns\BelongsToParentComponent;
    use ComponentContainer\Concerns\CanBeDisabled;
    use ComponentContainer\Concerns\CanBeHidden;
    use ComponentContainer\Concerns\CanBeValidated;
    use ComponentContainer\Concerns\Cloneable;
    use ComponentContainer\Concerns\HasComponents;
    use ComponentContainer\Concerns\HasEntryWrapper;
    use ComponentContainer\Concerns\HasFieldWrapper;
    use ComponentContainer\Concerns\HasInlineLabels;
    use ComponentContainer\Concerns\HasKey;
    use ComponentContainer\Concerns\HasOperation;
    use ComponentContainer\Concerns\HasState;
    use Components\Concerns\HasColumns;
    use Components\Concerns\HasStateBindingModifiers;
    use HasExtraAttributes;

    protected string $view = 'filament-schema::component-container';

    protected string $evaluationIdentifier = 'container';

    protected string $viewIdentifier = 'container';

    final public function __construct(Component & HasSchemas $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(Component & HasSchemas $livewire): static
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
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
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
