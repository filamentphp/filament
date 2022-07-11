<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component as LivewireComponent;

class Wizard extends Component
{
    use HasExtraAlpineAttributes;

    protected string | Htmlable | null $cancelAction = null;

    protected string | Htmlable | null $submitAction = null;

    public int | Closure $startStep = 1;

    protected string $view = 'forms::components.wizard';

    final public function __construct(array | Closure $steps = [])
    {
        $this->steps($steps);
    }

    public static function make(array | Closure $steps = []): static
    {
        $static = app(static::class, ['steps' => $steps]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'wizard::nextStep' => [
                function (Wizard $component, string $statePath, string $currentStep): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    /** @var Step $currentStep */
                    $currentStep = $component->getChildComponentContainer()->getComponents()[$currentStep];

                    $currentStep->getChildComponentContainer()->validate();
                    $currentStep->callAfterValidated();

                    /** @var LivewireComponent $livewire */
                    $livewire = $component->getLivewire();
                    $livewire->dispatchBrowserEvent('next-wizard-step', [
                        'statePath' => $statePath,
                    ]);
                },
            ],
        ]);
    }

    public function steps(array | Closure $steps): static
    {
        $this->childComponents($steps);

        return $this;
    }

    public function startOnStep(int | Closure $startStep): static
    {
        $this->startStep = $startStep;

        return $this;
    }

    public function cancelAction(string | Htmlable | null $action): static
    {
        $this->cancelAction = $action;

        return $this;
    }

    public function submitAction(string | Htmlable | null $action): static
    {
        $this->submitAction = $action;

        return $this;
    }

    public function getCancelAction(): string | Htmlable | null
    {
        return $this->cancelAction;
    }

    public function getSubmitAction(): string | Htmlable | null
    {
        return $this->submitAction;
    }

    public function getStartStep(): int
    {
        return $this->evaluate($this->startStep);
    }
}
