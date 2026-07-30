<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\View\SchemaIconAlias;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;

use function Filament\Support\generate_icon_html;

class Wizard extends Component implements HasEmbeddedView
{
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;
    use HasLabel;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.wizard';

    protected string | Htmlable | null $cancelAction = null;

    protected bool | Closure $isSkippable = false;

    protected string | Closure | null $stepQueryStringKey = null;

    protected string | Htmlable | null $submitAction = null;

    protected ?Closure $modifyNextActionUsing = null;

    protected ?Closure $modifyPreviousActionUsing = null;

    protected int | Closure $startStep = 1;

    protected ?int $currentStepIndex = null;

    protected string | Closure | null $alpineSubmitHandler = null;

    protected bool | Closure $isHeaderHidden = false;

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

        $this->key(function (Wizard $component): string {
            $statePath = $component->getStatePath();
            $label = $component->getLabel();

            if (blank($label)) {
                return filled($statePath) ? "{$statePath}::wizard" : 'wizard';
            }

            return Str::slug(Str::transliterate($label, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::wizard" : 'wizard');
        }, isInheritable: false);

        $this->registerActions([
            fn (Wizard $component): Action => $component->getNextAction(),
            fn (Wizard $component): Action => $component->getPreviousAction(),
        ]);
    }

    #[ExposedLivewireMethod]
    public function nextStep(int $currentStepIndex): void
    {
        if (! $this->isSkippable()) {
            $steps = array_values(
                $this
                    ->getChildSchema()
                    ->getComponents()
            );

            /** @var Step $currentStep */
            $currentStep = $steps[$currentStepIndex];

            /** @var ?Step $nextStep */
            $nextStep = $steps[$currentStepIndex + 1] ?? null;
            $this->currentStepIndex($currentStepIndex + 1);

            try {
                $currentStep->callBeforeValidation();
                $currentStep->getChildSchema()->validate();
                $currentStep->callAfterValidation();
                $nextStep?->fillStateWithNull();
            } catch (Halt $exception) {
                return;
            }
        }

        /** @var HasSchemas&LivewireComponent $livewire */
        $livewire = $this->getLivewire();
        $livewire->dispatch('next-wizard-step', key: $this->getKey());
    }

    #[ExposedLivewireMethod]
    public function previousStep(int $currentStepIndex): void
    {
        if ($currentStepIndex < 1) {
            $currentStepIndex = 1;
        }

        $this->currentStepIndex($currentStepIndex - 1);
    }

    public function goToStep(string $step): void
    {
        $steps = array_values(
            $this->getChildSchema()->getComponents()
        );

        foreach ($steps as $index => $wizardStep) {
            if ($wizardStep->getKey() !== $step) {
                continue;
            }

            if ((! $this->isSkippable()) && ($index > $this->getCurrentStepIndex())) {
                return;
            }

            $this->currentStepIndex($index);

            /** @var HasSchemas&LivewireComponent $livewire */
            $livewire = $this->getLivewire();

            $livewire->dispatch(
                'go-to-wizard-step',
                key: $this->getKey(),
                step: $step,
            );

            return;
        }
    }

    public function getNextAction(): Action
    {
        $action = Action::make($this->getNextActionName())
            ->label(__('filament-schemas::components.wizard.actions.next_step.label'))
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
            ->label(__('filament-schemas::components.wizard.actions.previous_step.label'))
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
        $this->components($steps);

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

            foreach ($this->getChildSchema()->getComponents() as $index => $step) {
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
        return $this->currentStepIndex ??= ($this->getStartStep() - 1);
    }

    protected function currentStepIndex(int $index): static
    {
        $this->currentStepIndex = $index;

        return $this;
    }

    public function alpineSubmitHandler(string | Closure | null $handler): static
    {
        $this->alpineSubmitHandler = $handler;

        return $this;
    }

    public function getAlpineSubmitHandler(): ?string
    {
        return $this->evaluate($this->alpineSubmitHandler);
    }

    public function hiddenHeader(bool | Closure $condition = true): static
    {
        $this->isHeaderHidden = $condition;

        return $this;
    }

    public function isHeaderHidden(): bool
    {
        return (bool) $this->evaluate($this->isHeaderHidden);
    }

    public function toEmbeddedHtml(): string
    {
        $isContained = $this->isContained();
        $key = $this->getKey();
        $previousAction = $this->getAction('previous');
        $nextAction = $this->getAction('next');
        $steps = array_filter(
            $this->getChildSchema()->getComponents(),
            static fn ($component): bool => $component instanceof Step,
        );
        $isHeaderHidden = $this->isHeaderHidden();

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'id' => $this->getId(),
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-sc-wizard',
                'fi-contained' => $isContained,
                'fi-sc-wizard-header-hidden' => $isHeaderHidden,
            ]);

        ob_start(); ?>

        <div
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('wizard', 'filament/schemas')) ?>"
            x-data="wizardSchemaComponent({
                        isSkippable: <?= Js::from($this->isSkippable()) ?>,
                        isStepPersistedInQueryString: <?= Js::from($this->isStepPersistedInQueryString()) ?>,
                        key: <?= Js::from($key) ?>,
                        livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                        schemaKey: <?= Js::from($this->getRootContainer()->getKey()) ?>,
                        startStep: <?= Js::from($this->getStartStep()) ?>,
                        stepQueryStringKey: <?= Js::from($this->getStepQueryStringKey()) ?>,
                    })"
            x-on:next-wizard-step.window="if ($event.detail.key === <?= Js::from($key) ?>) goToNextStep()"
            x-on:go-to-wizard-step.window="$event.detail.key === <?= Js::from($key) ?> && goToStep($event.detail.step)"
            wire:ignore.self
            <?= $outerAttributes->toHtml() ?>
        >
            <input
                type="hidden"
                value="<?= e(collect($steps)
                ->filter(static fn (Step $step): bool => $step->isVisible())
                ->map(static fn (Step $step): ?string => $step->getKey())
                ->values()
                ->toJson()) ?>"
                x-ref="stepsData"
            />

            <?php if (! $isHeaderHidden) { ?>
                <ol
                    <?php if (filled($label = $this->getLabel())) { ?>
                        aria-label="<?= e($label) ?>"
                    <?php } ?>
                    role="list"
                    x-cloak
                    x-ref="header"
                    class="fi-sc-wizard-header"
                >
                    <?php $stepIndex = 0; ?>
                    <?php $stepCount = count($steps); ?>
                    <?php foreach ($steps as $step) { ?>
                        <li
                            class="fi-sc-wizard-header-step"
                            x-bind:class="{
                                'fi-active': getStepIndex(step) === <?= $stepIndex ?>,
                                'fi-completed': getStepIndex(step) > <?= $stepIndex ?>,
                            }"
                        >
                            <button
                                type="button"
                                <?php if (filled($stepId = $step->getId())) { ?>
                                    id="<?= e($stepId) ?>-tab"
                                    aria-controls="<?= e($stepId) ?>"
                                <?php } ?>
                                x-bind:aria-current="getStepIndex(step) === <?= $stepIndex ?> ? 'step' : null"
                                x-on:click="step = <?= Js::from($step->getKey()) ?>"
                                x-bind:disabled="! isStepAccessible(<?= Js::from($step->getKey()) ?>) || <?= Js::from($previousAction->isDisabled()) ?>"
                                class="fi-sc-wizard-header-step-btn"
                            >
                                <div class="fi-sc-wizard-header-step-icon-ctn">
                                    <?php $completedIcon = $step->getCompletedIcon(); ?>

                                    <?= generate_icon_html(
                                        $completedIcon ?? Heroicon::OutlinedCheck,
                                        alias: filled($completedIcon) ? null : SchemaIconAlias::COMPONENTS_WIZARD_COMPLETED_STEP,
                                        attributes: new FilamentComponentAttributeBag([
                                            'x-cloak' => 'x-cloak',
                                            'x-show' => "getStepIndex(step) > {$stepIndex}",
                                        ]),
                                        size: IconSize::Large,
                                    )?->toHtml() ?>

                                    <?php if (filled($icon = $step->getIcon())) { ?>
                                        <?= generate_icon_html(
                                            $icon,
                                            attributes: new FilamentComponentAttributeBag([
                                                'x-cloak' => 'x-cloak',
                                                'x-show' => "getStepIndex(step) <= {$stepIndex}",
                                            ]),
                                            size: IconSize::Large,
                                        )?->toHtml() ?>
                                    <?php } else { ?>
                                        <span
                                            x-show="getStepIndex(step) <= <?= $stepIndex ?>"
                                            aria-hidden="true"
                                            class="fi-sc-wizard-header-step-number"
                                        >
                                            <?= str_pad((string) ($stepIndex + 1), 2, '0', STR_PAD_LEFT) ?>
                                        </span>
                                    <?php } ?>
                                </div>

                                <div class="fi-sc-wizard-header-step-text">
                                    <?php // Always render the label so the button (and the panel it labels via `aria-labelledby`) keeps an accessible name; visually hide it when `hiddenLabel()` is set.?>
                                    <span class="fi-sc-wizard-header-step-label<?= $step->isLabelHidden() ? ' fi-sr-only' : '' ?>"><?= e($step->getLabel()) ?></span>

                                    <?php if (filled($description = $step->getDescription())) { ?>
                                        <span class="fi-sc-wizard-header-step-description"><?= e($description) ?></span>
                                    <?php } ?>

                                    <?php // Announce completed/upcoming state to screen readers. The current step is owned by `aria-current="step"`, so its status text stays empty to avoid a redundant, doubly-announced name.?>
                                    <span
                                        class="fi-sr-only"
                                        x-text="getStepIndex(step) > <?= $stepIndex ?> ? <?= Js::from(__('filament-schemas::components.wizard.header.step.statuses.completed')) ?> : (getStepIndex(step) === <?= $stepIndex ?> ? '' : <?= Js::from(__('filament-schemas::components.wizard.header.step.statuses.upcoming')) ?>)"
                                    ></span>
                                </div>
                            </button>

                            <?php if ($stepIndex < $stepCount - 1) { ?>
                                <svg fill="none" preserveAspectRatio="none" viewBox="0 0 22 80" aria-hidden="true" class="fi-sc-wizard-header-step-separator">
                                    <path d="M0 -2L20 40L0 82" stroke-linejoin="round" stroke="currentcolor" vector-effect="non-scaling-stroke"></path>
                                </svg>
                            <?php } ?>
                        </li>
                        <?php $stepIndex++; ?>
                    <?php } ?>
                </ol>
            <?php } ?>

            <?php foreach ($steps as $step) { ?>
                <?= $step->toHtml() ?>
            <?php } ?>

            <div x-cloak class="fi-sc-wizard-footer">
                <div
                    x-cloak
                    <?php if (! $previousAction->isDisabled()) { ?>
                        x-on:click="goToPreviousStep"
                    <?php } ?>
                    x-show="! isFirstStep()"
                >
                    <?= $previousAction->toHtml() ?>
                </div>

                <div x-show="isFirstStep()">
                    <?= e($this->getCancelAction()) ?>
                </div>

                <div
                    x-cloak
                    <?php if (! $nextAction->isDisabled()) { ?>
                        x-on:click="requestNextStep()"
                    <?php } ?>
                    x-bind:class="{ 'fi-hidden': isLastStep() }"
                    wire:loading.class="fi-disabled"
                >
                    <?= $nextAction->toHtml() ?>
                </div>

                <div x-bind:class="{ 'fi-hidden': ! isLastStep() }">
                    <?= e($this->getSubmitAction()) ?>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
