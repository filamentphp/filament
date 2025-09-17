<?php

namespace Filament\Actions\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\ModalComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

trait CanOpenModal
{
    /**
     * @var array<string, Action>
     */
    protected array $cachedExtraModalFooterActions;

    /**
     * @var array<Action> | Closure
     */
    protected array | Closure $extraModalFooterActions = [];

    protected bool | Closure | null $isModalFooterSticky = null;

    protected bool | Closure | null $isModalHeaderSticky = null;

    /**
     * @var array<string, Action>
     */
    protected array $cachedModalActions;

    /**
     * @var array<Action | Closure>
     */
    protected array $modalActions = [];

    protected bool | Closure $isModalSlideOver = false;

    protected Alignment | string | Closure | null $modalAlignment = null;

    /**
     * @var array<string, Action>
     */
    protected array $cachedModalFooterActions;

    /**
     * @var array<Action> | Closure | null
     */
    protected array | Closure | null $modalFooterActions = null;

    protected Alignment | string | Closure | null $modalFooterActionsAlignment = null;

    protected Action | bool | Closure | null $modalCancelAction = null;

    protected string | Closure | null $modalCancelActionLabel = null;

    protected Action | bool | Closure | null $modalSubmitAction = null;

    protected string | Closure | null $modalSubmitActionLabel = null;

    protected View | Htmlable | Closure | null $modalContent = null;

    protected View | Htmlable | Closure | null $modalContentFooter = null;

    protected string | Htmlable | Closure | null $modalHeading = null;

    protected string | Htmlable | Closure | null $modalDescription = null;

    protected Width | string | Closure | null $modalWidth = null;

    protected bool | Closure | null $hasModal = null;

    protected bool | Closure | null $isModalHidden = null;

    protected bool | Closure | null $hasModalCloseButton = null;

    protected bool | Closure | null $isModalClosedByClickingAway = null;

    protected bool | Closure | null $isModalClosedByEscaping = null;

    protected bool | Closure | null $isModalAutofocused = null;

    protected string | BackedEnum | Closure | null $modalIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $modalIconColor = null;

    public function closeModalByClickingAway(bool | Closure | null $condition = true): static
    {
        $this->isModalClosedByClickingAway = $condition;

        return $this;
    }

    public function closeModalByEscaping(bool | Closure | null $condition = true): static
    {
        $this->isModalClosedByEscaping = $condition;

        return $this;
    }

    /**
     * @deprecated Use `modalAlignment(Alignment::Center)` instead.
     */
    public function centerModal(bool | Closure | null $condition = true): static
    {
        if ($this->evaluate($condition)) {
            $this->modalAlignment(Alignment::Center);
        }

        return $this;
    }

    public function modalAlignment(Alignment | string | Closure | null $alignment = null): static
    {
        $this->modalAlignment = $alignment;

        return $this;
    }

    public function modalCloseButton(bool | Closure | null $condition = true): static
    {
        $this->hasModalCloseButton = $condition;

        return $this;
    }

    public function modalAutofocus(bool | Closure | null $condition = true): static
    {
        $this->isModalAutofocused = $condition;

        return $this;
    }

    public function modalIcon(string | BackedEnum | Closure | null $icon = null): static
    {
        $this->modalIcon = $icon;

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function modalIconColor(string | array | Closure | null $color = null): static
    {
        $this->modalIconColor = $color;

        return $this;
    }

    public function slideOver(bool | Closure $condition = true): static
    {
        $this->isModalSlideOver = $condition;

        return $this;
    }

    /**
     * @param  array<Action> | Closure | null  $actions
     *
     *@deprecated Use `modalFooterActions()` instead.
     */
    public function modalActions(array | Closure | null $actions = null): static
    {
        $this->modalFooterActions($actions);

        return $this;
    }

    /**
     * @param  array<Action> | Closure | null  $actions
     */
    public function modalFooterActions(array | Closure | null $actions = null): static
    {
        $this->modalFooterActions = $actions;

        return $this;
    }

    public function modalFooterActionsAlignment(Alignment | string | Closure | null $alignment = null): static
    {
        $this->modalFooterActionsAlignment = $alignment;

        return $this;
    }

    /**
     * @param  array<Action> | Closure  $actions
     *
     *@deprecated Use `extraModalFooterActions()` instead.
     */
    public function extraModalActions(array | Closure $actions): static
    {
        $this->extraModalFooterActions($actions);

        return $this;
    }

    /**
     * @param  array<Action> | Closure  $actions
     */
    public function extraModalFooterActions(array | Closure $actions): static
    {
        $this->extraModalFooterActions = $actions;

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function registerModalActions(array $actions): static
    {
        $this->modalActions = [
            ...$this->modalActions,
            ...$actions,
        ];

        return $this;
    }

    public function modalSubmitAction(Action | bool | Closure | null $action = null): static
    {
        $this->modalSubmitAction = $action;

        return $this;
    }

    public function modalCancelAction(Action | bool | Closure | null $action = null): static
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

    /**
     * @deprecated Use `modalContentFooter()` instead.
     */
    public function modalFooter(View | Htmlable | Closure | null $footer = null): static
    {
        return $this->modalContentFooter($footer);
    }

    public function modalContentFooter(View | Htmlable | Closure | null $footer = null): static
    {
        $this->modalContentFooter = $footer;

        return $this;
    }

    public function modalHeading(string | Htmlable | Closure | null $heading = null): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalDescription(string | Htmlable | Closure | null $description = null): static
    {
        $this->modalDescription = $description;

        return $this;
    }

    /**
     * @deprecated Use `modalDescription()` instead.
     */
    public function modalSubheading(string | Htmlable | Closure | null $subheading = null): static
    {
        $this->modalDescription($subheading);

        return $this;
    }

    public function modalWidth(Width | string | Closure | null $width = null): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    public function getLivewireCallMountedActionName(): ?string
    {
        return null;
    }

    public function modal(bool | Closure | null $condition = true): static
    {
        $this->hasModal = $condition;

        return $this;
    }

    public function modalHidden(bool | Closure | null $condition = true): static
    {
        $this->isModalHidden = $condition;

        return $this;
    }

    /**
     * @return array<string, Action>
     */
    public function getModalFooterActions(): array
    {
        if ($this->isWizard()) {
            return [];
        }

        if (isset($this->cachedModalFooterActions)) {
            return $this->cachedModalFooterActions;
        }

        if ($this->modalFooterActions) {
            $actions = [];

            foreach ($this->evaluate($this->modalFooterActions) as $modalAction) {
                $actions[$modalAction->getName()] = $this->prepareModalAction($modalAction);
            }

            return $this->cachedModalFooterActions = $actions;
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

        if (in_array($this->getModalFooterActionsAlignment(), [Alignment::Center, 'center'])) {
            $actions = array_reverse($actions);
        }

        return $this->cachedModalFooterActions = $actions;
    }

    public function getModalFooterActionsAlignment(): string | Alignment | null
    {
        if ($alignment = $this->evaluate($this->modalFooterActionsAlignment)) {
            return $alignment;
        }

        if ($this->isConfirmationRequired()) {
            return Alignment::Center;
        }

        return null;
    }

    /**
     * @return array<string, Action>
     */
    public function getModalActions(): array
    {
        if (isset($this->cachedModalActions)) {
            return $this->cachedModalActions;
        }

        $actions = $this->getModalFooterActions();

        foreach ($this->modalActions as $action) {
            foreach (Arr::wrap($this->evaluate($action)) as $modalAction) {
                $actions[$modalAction->getName()] = $this->prepareModalAction($modalAction);
            }
        }

        return $this->cachedModalActions = $actions;
    }

    public function getModalAction(string $name): ?Action
    {
        return $this->getModalActions()[$name] ?? null;
    }

    public function prepareModalAction(Action $action): Action
    {
        return $action
            ->schemaContainer($this->getSchemaContainer())
            ->schemaComponent($this->getSchemaComponent())
            ->livewire($this->getLivewire())
            ->when(
                ! $action->hasRecord(),
                fn (Action $action) => $action->record($this->getRecord()),
            )
            ->table($this->getTable());
    }

    /**
     * @return array<Action>
     */
    public function getVisibleModalFooterActions(): array
    {
        return array_filter(
            $this->getModalFooterActions(),
            fn (Action $action): bool => $action->isVisible(),
        );
    }

    public function getModalSubmitAction(): ?Action
    {
        $hasFormWrapper = $this->hasFormWrapper();

        $action = static::makeModalAction('submit')
            ->label($this->getModalSubmitActionLabel())
            ->submit($hasFormWrapper ? $this->getLivewireCallMountedActionName() : null)
            ->action($hasFormWrapper ? null : $this->getLivewireCallMountedActionName())
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

    public function getModalCancelAction(): ?Action
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
     * @return array<Action>
     */
    public function getExtraModalFooterActions(): array
    {
        if (isset($this->cachedExtraModalFooterActions)) {
            return $this->cachedExtraModalFooterActions;
        }

        $actions = [];

        foreach ($this->evaluate($this->extraModalFooterActions) as $action) {
            $actions[$action->getName()] = $this->prepareModalAction($action);
        }

        return $this->cachedExtraModalFooterActions = $actions;
    }

    public function getModalAlignment(): Alignment | string
    {
        if ($alignment = $this->evaluate($this->modalAlignment)) {
            return $alignment;
        }

        if ($this->isConfirmationRequired() || in_array($this->getModalWidth(), [Width::ExtraSmall, Width::Small, 'xs', 'sm'])) {
            return Alignment::Center;
        }

        return Alignment::Start;
    }

    public function getModalSubmitActionLabel(): string
    {
        if (filled($label = $this->evaluate($this->modalSubmitActionLabel))) {
            return $label;
        }

        if ($this->isConfirmationRequired()) {
            return __('filament-actions::modal.actions.confirm.label');
        }

        return __('filament-actions::modal.actions.submit.label');
    }

    public function getModalCancelActionLabel(): string
    {
        return $this->evaluate($this->modalCancelActionLabel) ?? __('filament-actions::modal.actions.cancel.label');
    }

    public function getModalContent(): View | Htmlable | null
    {
        return $this->evaluate($this->modalContent);
    }

    public function getModalContentFooter(): View | Htmlable | null
    {
        return $this->evaluate($this->modalContentFooter);
    }

    public function hasModalContent(): bool
    {
        return $this->modalContent !== null;
    }

    public function hasModalContentFooter(): bool
    {
        return $this->modalContentFooter !== null;
    }

    public function getCustomModalHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->modalHeading);
    }

    public function getModalHeading(): string | Htmlable
    {
        return $this->getCustomModalHeading() ?? $this->getLabel();
    }

    public function hasCustomModalHeading(): bool
    {
        return filled($this->getCustomModalHeading());
    }

    public function getModalDescription(): string | Htmlable | null
    {
        if (filled($description = $this->evaluate($this->modalDescription))) {
            return $description;
        }

        if ($this->isConfirmationRequired()) {
            return __('filament-actions::modal.confirmation');
        }

        return null;
    }

    public function hasModalDescription(): bool
    {
        return filled($this->getModalDescription());
    }

    public function getModalWidth(): Width | string
    {
        if ($width = $this->evaluate($this->modalWidth)) {
            return $width;
        }

        if ($this->isConfirmationRequired()) {
            return Width::Medium;
        }

        return Width::FourExtraLarge;
    }

    public function isModalFooterSticky(): bool
    {
        return (bool) ($this->evaluate($this->isModalFooterSticky) ?? $this->isModalSlideOver());
    }

    public function isModalHeaderSticky(): bool
    {
        return (bool) ($this->evaluate($this->isModalHeaderSticky) ?? $this->isModalSlideOver());
    }

    public function isModalSlideOver(): bool
    {
        return (bool) $this->evaluate($this->isModalSlideOver);
    }

    public function shouldOpenModal(?Closure $checkForSchemaUsing = null): bool
    {
        if (is_bool($hasModal = $this->evaluate($this->hasModal))) {
            return $hasModal;
        }

        if ($this->evaluate($this->isModalHidden)) {
            return false;
        }

        return $this->hasCustomModalHeading() ||
            $this->hasModalDescription() ||
            $this->hasModalContent() ||
            $this->hasModalContentFooter() ||
            (value($checkForSchemaUsing, $this) ?? false);
    }

    public function hasModalCloseButton(): bool
    {
        return $this->evaluate($this->hasModalCloseButton) ?? ModalComponent::$hasCloseButton;
    }

    public function isModalClosedByClickingAway(): bool
    {
        return (bool) ($this->evaluate($this->isModalClosedByClickingAway) ?? ModalComponent::$isClosedByClickingAway);
    }

    public function isModalClosedByEscaping(): bool
    {
        return (bool) ($this->evaluate($this->isModalClosedByEscaping) ?? ModalComponent::$isClosedByEscaping);
    }

    public function isModalAutofocused(): bool
    {
        return $this->evaluate($this->isModalAutofocused) ?? ModalComponent::$isAutofocused;
    }

    /**
     * @deprecated Use `makeModalSubmitAction()` instead.
     *
     * @param  array<string, mixed> | null  $arguments
     */
    public function makeExtraModalAction(string $name, ?array $arguments = null): Action
    {
        return $this->makeModalSubmitAction($name, $arguments);
    }

    /**
     * @param  array<string, mixed> | null  $arguments
     */
    public function makeModalSubmitAction(string $name, ?array $arguments = null): Action
    {
        return static::makeModalAction($name)
            ->callParent($this->getLivewireCallMountedActionName())
            ->arguments($arguments)
            ->color('gray');
    }

    public function makeModalAction(string $name): Action
    {
        return Action::make($name)
            ->button();
    }

    public function getModalIcon(): string | BackedEnum | null
    {
        if ($icon = $this->evaluate($this->modalIcon)) {
            return $icon;
        }

        if ($this->isConfirmationRequired()) {
            return FilamentIcon::resolve(ActionsIconAlias::MODAL_CONFIRMATION) ?? Heroicon::OutlinedExclamationTriangle;
        }

        return null;
    }

    /**
     * @return string | array<string> | null
     */
    public function getModalIconColor(): string | array | null
    {
        return $this->evaluate($this->modalIconColor) ?? $this->getColor() ?? 'primary';
    }

    public function stickyModalFooter(bool | Closure $condition = true): static
    {
        $this->isModalFooterSticky = $condition;

        return $this;
    }

    public function stickyModalHeader(bool | Closure $condition = true): static
    {
        $this->isModalHeaderSticky = $condition;

        return $this;
    }
}
