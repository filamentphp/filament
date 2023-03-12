<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component as LivewireComponent;

class Wizard extends Component
{
    use HasExtraAlpineAttributes;

    protected string | Htmlable | null $cancelAction = null;

    protected bool | Closure $skippable = false;

    protected string | Closure | null $stepQueryStringKey = null;

    protected string | Htmlable | null $submitAction = null;

    protected ?Closure $modifyNextActionUsing = null;

    protected ?Closure $modifyPreviousActionUsing = null;

    public int | Closure $startStep = 1;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.wizard';

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

        $this->registerActions([
            fn (Wizard $component): ?Action => $component->getNextAction(),
            fn (Wizard $component): ?Action => $component->getPreviousAction(),
        ]);

        $this->registerListeners([
            'wizard::nextStep' => [
                function (Wizard $component, string $statePath, string $currentStep): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    if (! $component->isSkippable()) {
                        /** @var Step $currentStep */
                        $currentStep = $component->getChildComponentContainer()->getComponents()[$currentStep];

                        $currentStep->callBeforeValidation();
                        $currentStep->getChildComponentContainer()->validate();
                        $currentStep->callAfterValidation();
                    }

                    /** @var LivewireComponent $livewire */
                    $livewire = $component->getLivewire();
                    $livewire->dispatchBrowserEvent('next-wizard-step', [
                        'statePath' => $statePath,
                    ]);
                },
            ],
        ]);
    }

    public function getNextAction(): ?Action
    {
        $action = Action::make($this->getNextActionName())
            ->label(__('filament-forms::components.wizard.actions.next_step.label'))
            ->icon((__('filament::layout.direction') === 'rtl') ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right')
            ->iconPosition('after')
            ->mountedOnClick(false)
            ->button()
            ->outlined()
            ->size('sm');

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

    public function getPreviousAction(): ?Action
    {
        $action = Action::make($this->getPreviousActionName())
            ->label(__('filament-forms::components.wizard.actions.previous_step.label'))
            ->icon((__('filament::layout.direction') === 'rtl') ? 'heroicon-m-chevron-right' : 'heroicon-m-chevron-left')
            ->color('gray')
            ->mountedOnClick(false)
            ->button()
            ->size('sm');

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
        $this->skippable = $condition;

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

            foreach ($this->getChildComponents() as $index => $step) {
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
        return (bool) $this->evaluate($this->skippable);
    }

    public function isStepPersistedInQueryString(): bool
    {
        return filled($this->getStepQueryStringKey());
    }
}
