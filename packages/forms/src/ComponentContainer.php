<?php

namespace Filament\Forms;

use Filament\Forms\Contracts\HasForms;
use Filament\Support\Components\ViewComponent;
use ReflectionParameter;

class ComponentContainer extends ViewComponent
{
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToModel;
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeValidated;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasFieldWrapper;
    use Concerns\HasInlineLabels;
    use Concerns\HasOperation;
    use Concerns\HasState;
    use Concerns\HasStateBindingModifiers;
    use Concerns\ListensToEvents;
    use Concerns\SupportsComponentFileAttachments;
    use Concerns\SupportsFileUploadFields;
    use Concerns\SupportsSelectFields;

    protected string $view = 'filament-forms::component-container';

    protected string $evaluationIdentifier = 'container';

    protected string $viewIdentifier = 'container';

    final public function __construct(HasForms $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasForms $livewire): static
    {
        return app(static::class, ['livewire' => $livewire]);
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'livewire' => $this->getLivewire(),
            'model' => $this->getModel(),
            'record' => $this->getRecord(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
