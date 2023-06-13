<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Filament\Support\View\Components\Modal;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait CanOpenModal
{
    /**
     * @var array<StaticAction> | Closure
     */
    protected array | Closure $extraModalFooterActions = [];

    /**
     * @var array<StaticAction>
     */
    protected array $modalActions = [];

    protected bool | Closure | null $isModalCentered = null;

    protected bool | Closure $isModalSlideOver = false;

    /**
     * @var array<StaticAction> | Closure | null
     */
    protected array | Closure | null $modalFooterActions = null;

    protected StaticAction | bool | Closure | null $modalCancelAction = null;

    protected string | Closure | null $modalCancelActionLabel = null;

    protected StaticAction | bool | Closure | null $modalSubmitAction = null;

    protected string | Closure | null $modalSubmitActionLabel = null;

    protected View | Htmlable | Closure | null $modalContent = null;

    protected View | Htmlable | Closure | null $modalFooter = null;

    protected string | Htmlable | Closure | null $modalHeading = null;

    protected string | Htmlable | Closure | null $modalSubheading = null;

    protected string | Closure | null $modalWidth = null;

    protected bool | Closure | null $isModalHidden = false;

    protected bool | Closure | null $isModalCloseButtonDisplayed = null;

    protected bool | Closure | null $isModalClosedByClickingAway = null;

    public function closeModalByClickingAway(bool | Closure | null $condition = true): static
    {
        $this->isModalClosedByClickingAway = $condition;

        return $this;
    }

    public function centerModal(bool | Closure | null $condition = true): static
    {
        $this->isModalCentered = $condition;

        return $this;
    }

    public function displayModalCloseButton(bool | Closure | null $condition = true): static
    {
        $this->isModalCloseButtonDisplayed = $condition;

        return $this;
    }

    public function slideOver(bool | Closure $condition = true): static
    {
        $this->isModalSlideOver = $condition;

        return $this;
    }

    /**
     * @deprecated Use `modalFooterActions()` instead.
     *
     * @param  array<StaticAction> | Closure | null  $actions
     */
    public function modalActions(array | Closure | null $actions = null): static
    {
        $this->modalFooterActions($actions);

        return $this;
    }

    /**
     * @param  array<StaticAction> | Closure | null  $actions
     */
    public function modalFooterActions(array | Closure | null $actions = null): static
    {
        if (! is_array($actions)) {
            $this->modalFooterActions = $actions;

            return $this;
        }

        $this->modalFooterActions = [];

        foreach ($actions as $action) {
            $this->modalFooterActions[$action->getName()] = $action;
        }

        return $this;
    }

    /**
     * @deprecated Use `extraModalFooterActions()` instead.
     *
     * @param  array<StaticAction> | Closure  $actions
     */
    public function extraModalActions(array | Closure $actions): static
    {
        $this->extraModalFooterActions($actions);

        return $this;
    }

    /**
     * @param  array<StaticAction> | Closure  $actions
     */
    public function extraModalFooterActions(array | Closure $actions): static
    {
        $this->extraModalFooterActions = $actions;

        return $this;
    }

    /**
     * @param  array<StaticAction>  $actions
     */
    public function registerModalActions(array $actions): static
    {
        $this->modalActions = [
            ...$this->modalActions,
            ...$actions,
        ];

        return $this;
    }

    public function modalSubmitAction(StaticAction | bool | Closure | null $action = null): static
    {
        $this->modalSubmitAction = $action;

        return $this;
    }

    public function modalCancelAction(StaticAction | bool | Closure | null $action = null): static
    {
        $this->modalCancelAction = $action;

        return $this;
    }

    public function modalSubmitActionLabel(string | Closure | null $label = null): static
    {
        $this->modalSubmitActionLabel = $label;

        return $this;
    }

    public function modalCancelActionLabel(string | Closure | null $label = null): static
    {
        $this->modalCancelActionLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `modalSubmitActionLabel()` instead.
     */
    public function modalButton(string | Closure | null $label = null): static
    {
        $this->modalSubmitActionLabel($label);

        return $this;
    }

    public function modalContent(View | Htmlable | Closure | null $content = null): static
    {
        $this->modalContent = $content;

        return $this;
    }

    public function modalFooter(View | Htmlable | Closure | null $footer = null): static
    {
        $this->modalFooter = $footer;

        return $this;
    }

    public function modalHeading(string | Htmlable | Closure | null $heading = null): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalSubheading(string | Htmlable | Closure | null $subheading = null): static
    {
        $this->modalSubheading = $subheading;

        return $this;
    }

    public function modalWidth(string | Closure | null $width = null): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    public function getLivewireCallMountedActionName(): ?string
    {
        return null;
    }

    public function modalHidden(bool | Closure | null $condition = false): static
    {
        $this->isModalHidden = $condition;

        return $this;
    }

    /**
     * @return array<string, StaticAction>
     */
    public function getModalFooterActions(): array
    {
        if ($this->isWizard()) {
            return [];
        }

        if (is_array($this->modalFooterActions)) {
            return $this->modalFooterActions;
        }

        if ($this->modalFooterActions instanceof Closure) {
            $actions = [];

            foreach ($this->evaluate($this->modalFooterActions) as $action) {
                $actions[$action->getName()] = $this->prepareModalAction($action);
            }

            return $this->modalFooterActions = $actions;
        }

        $actions = [];

        if ($submitAction = $this->getModalSubmitAction()) {
            $actions['submit'] = $submitAction;
        }

        $actions = [
            ...$actions,
            ...$this->getExtraModalFooterActions(),
        ];

        if ($cancelAction = $this->getModalCancelAction()) {
            $actions['cancel'] = $cancelAction;
        }

        if ($this->isModalCentered()) {
            $actions = array_reverse($actions);
        }

        return $this->modalFooterActions = $actions;
    }

    /**
     * @return array<string, StaticAction>
     */
    public function getModalActions(): array
    {
        if (! count($this->modalActions)) {
            return [];
        }

        if (! is_numeric(array_key_first($this->modalActions))) {
            return $this->modalActions;
        }

        $actions = $this->getModalFooterActions();

        foreach ($this->modalActions as $action) {
            $actions[$action->getName()] = $this->prepareModalAction($action);
        }

        return $this->modalActions = $actions;
    }

    public function getModalAction(string $name): ?StaticAction
    {
        return $this->getModalActions()[$name] ?? null;
    }

    public function getMountableModalAction(string $name): ?MountableAction
    {
        $action = $this->getModalAction($name);

        if (! $action) {
            return null;
        }

        if (! $action instanceof MountableAction) {
            return null;
        }

        return $action;
    }

    public function prepareModalAction(StaticAction $action): StaticAction
    {
        if (! $action instanceof MountableAction) {
            return $action;
        }

        return $action
            ->livewire($this->getLivewire());
    }

    /**
     * @return array<StaticAction>
     */
    public function getVisibleModalActions(): array
    {
        return array_filter(
            $this->getModalFooterActions(),
            fn (StaticAction $action): bool => $action->isVisible(),
        );
    }

    public function getModalSubmitAction(): ?StaticAction
    {
        $action = static::makeModalAction('submit')
            ->label($this->getModalSubmitActionLabel())
            ->submit($this->getLivewireCallMountedActionName())
            ->color(match ($color = $this->getColor()) {
                'gray' => 'primary',
                default => $color,
            });

        if ($this->modalSubmitAction !== null) {
            $action = $this->evaluate($this->modalSubmitAction, ['action' => $action]) ?? $action;
        }

        if ($action === false) {
            return null;
        }

        return $action;
    }

    public function getModalCancelAction(): ?StaticAction
    {
        $action = static::makeModalAction('cancel')
            ->label($this->getModalCancelActionLabel())
            ->close()
            ->color('gray');

        if ($this->modalCancelAction !== null) {
            $action = $this->evaluate($this->modalCancelAction, ['action' => $action]) ?? $action;
        }

        if ($action === false) {
            return null;
        }

        return $action;
    }

    /**
     * @return array<StaticAction>
     */
    public function getExtraModalFooterActions(): array
    {
        $actions = [];

        foreach ($this->evaluate($this->extraModalFooterActions) as $action) {
            $actions[$action->getName()] = $this->prepareModalAction($action);
        }

        return $this->extraModalFooterActions = $actions;
    }

    public function getModalSubmitActionLabel(): string
    {
        return $this->evaluate($this->modalSubmitActionLabel) ?? __('filament-actions::modal.actions.submit.label');
    }

    public function getModalCancelActionLabel(): string
    {
        return $this->evaluate($this->modalCancelActionLabel) ?? __('filament-actions::modal.actions.cancel.label');
    }

    public function getModalContent(): View | Htmlable | null
    {
        return $this->evaluate($this->modalContent);
    }

    public function getModalFooter(): View | Htmlable | null
    {
        return $this->evaluate($this->modalFooter);
    }

    public function getModalHeading(): string | Htmlable
    {
        return $this->evaluate($this->modalHeading) ?? $this->getLabel();
    }

    public function getModalSubheading(): string | Htmlable | null
    {
        return $this->evaluate($this->modalSubheading);
    }

    public function getModalWidth(): string
    {
        return $this->evaluate($this->modalWidth) ?? '4xl';
    }

    public function isModalCentered(): bool
    {
        return $this->evaluate($this->isModalCentered) ?? in_array($this->getModalWidth(), ['xs', 'sm']);
    }

    public function isModalSlideOver(): bool
    {
        return (bool) $this->evaluate($this->isModalSlideOver);
    }

    public function isModalHidden(): bool
    {
        return $this->evaluate($this->isModalHidden);
    }

    public function isModalCloseButtonDisplayed(): bool
    {
        return $this->evaluate($this->isModalCloseButtonDisplayed) ?? Modal::$isCloseButtonDisplayed;
    }

    public function isModalClosedByClickingAway(): bool
    {
        return $this->evaluate($this->isModalClosedByClickingAway) ?? Modal::$isClosedByClickingAway;
    }

    /**
     * @deprecated Use `makeModalSubmitAction()` instead.
     *
     * @param  array<string, mixed> | null  $arguments
     */
    public function makeExtraModalAction(string $name, ?array $arguments = null): StaticAction
    {
        return $this->makeModalSubmitAction($name, $arguments);
    }

    /**
     * @param  array<string, mixed> | null  $arguments
     */
    public function makeModalSubmitAction(string $name, ?array $arguments = null): StaticAction
    {
        return static::makeModalAction($name)
            ->callParent($this->getLivewireCallMountedActionName())
            ->arguments($arguments)
            ->color('gray');
    }

    public function makeModalAction(string $name): StaticAction
    {
        return StaticAction::make($name)
            ->button();
    }
}
