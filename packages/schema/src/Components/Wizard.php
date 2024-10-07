<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Attributes\Exposed;
use Filament\Schema\Components\Wizard\Step;
use Filament\Support\Concerns;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component as LivewireComponent;

class Wizard extends Component
{
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;

    protected string | Htmlable | null $cancelAction = null;

    protected bool | Closure $isSkippable = false;

    protected string | Closure | null $stepQueryStringKey = null;

    protected string | Htmlable | null $submitAction = null;

    protected ?Closure $modifyNextActionUsing = null;

    protected ?Closure $modifyPreviousActionUsing = null;

    protected int | Closure $startStep = 1;

    protected int $currentStepIndex = 0;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.wizard';

    /**
     * @param  array<Step> | Closure  $steps
     */
    final public function __construct(array | Closure $steps = [])
    {
        $this->steps($steps);
    }

    /**
     * @param  array<Step> | Closure  $steps
     */
    public static function make(array | Closure $steps = []): static
    {
        $static = app(static::class, ['steps' => $steps]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key('wizard');

        $this->currentStepIndex($this->getStartStep() - 1);

        $this->registerActions([
            fn (Wizard $component): Action => $component->getNextAction(),
            fn (Wizard $component): Action => $component->getPreviousAction(),
        ]);
    }

    #[Exposed]
    public function nextStep(int $currentStepIndex): void
    {
        if (! $this->isSkippable()) {
            $steps = array_values(
                $this
                    ->getChildComponentContainer()
                    ->getComponents()
            );

            /** @var Step $currentStep */
            $currentStep = $steps[$currentStepIndex];

            /** @var ?Step $nextStep */
            $nextStep = $steps[$currentStepIndex + 1] ?? null;
            $this->currentStepIndex($currentStepIndex + 1);

            try {
                $currentStep->callBeforeValidation();
                $currentStep->getChildComponentContainer()->validate();
                $currentStep->callAfterValidation();
                $nextStep?->fillStateWithNull();
            } catch (Halt $exception) {
                return;
            }
        }

        /** @var LivewireComponent $livewire */
        $livewire = $this->getLivewire();
        $livewire->dispatch('next-wizard-step', key: $this->getKey());
    }

    #[Exposed]
    public function previousStep(int $currentStepIndex): void
    {
        if ($currentStepIndex < 1) {
            $currentStepIndex = 1;
        }

        $this->currentStepIndex($currentStepIndex - 1);
    }

    public function getNextAction(): Action
    {
        $action = Action::make($this->getNextActionName())
            ->label(__('filament-schema::components.wizard.actions.next_step.label'))
            ->iconPosition(IconPosition::After)
            ->livewireClickHandlerEnabled(false)
            ->livewireTarget('callSchemaComponentMethod')
            ->button();

        if ($this->modifyNextActionUsing) {
            $action = $this->evaluate($this->modifyNextActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function nextAction(?Closure $callback): static
    {
        $this->modifyNextActionUsing = $callback;

        return $this;
    }

    public function getNextActionName(): string
    {
        return 'next';
    }

    public function getPreviousAction(): Action
    {
        $action = Action::make($this->getPreviousActionName())
            ->label(__('filament-schema::components.wizard.actions.previous_step.label'))
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->button();

        if ($this->modifyPreviousActionUsing) {
            $action = $this->evaluate($this->modifyPreviousActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function previousAction(?Closure $callback): static
    {
        $this->modifyPreviousActionUsing = $callback;

        return $this;
    }

    public function getPreviousActionName(): string
    {
        return 'previous';
    }

    /**
     * @param  array<Step> | Closure  $steps
     */
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

    public function skippable(bool | Closure $condition = true): static
    {
        $this->isSkippable = $condition;

        return $this;
    }

    public function persistStepInQueryString(string | Closure | null $key = 'step'): static
    {
        $this->stepQueryStringKey = $key;

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
        if ($this->isStepPersistedInQueryString()) {
            $queryStringStep = request()->query($this->getStepQueryStringKey());

            foreach ($this->getChildComponentContainer()->getComponents() as $index => $step) {
                if ($step->getId() !== $queryStringStep) {
                    continue;
                }

                return $index + 1;
            }
        }

        return $this->evaluate($this->startStep);
    }

    public function getStepQueryStringKey(): ?string
    {
        return $this->evaluate($this->stepQueryStringKey);
    }

    public function isSkippable(): bool
    {
        return (bool) $this->evaluate($this->isSkippable);
    }

    public function isStepPersistedInQueryString(): bool
    {
        return filled($this->getStepQueryStringKey());
    }

    public function getCurrentStepIndex(): int
    {
        return $this->currentStepIndex;
    }

    protected function currentStepIndex(int $index): static
    {
        $this->currentStepIndex = $index;

        return $this;
    }
}
