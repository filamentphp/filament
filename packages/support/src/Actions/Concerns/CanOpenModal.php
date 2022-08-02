<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Support\Actions\Modal\Actions\Action as ModalAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

trait CanOpenModal
{
    protected array | Closure $extraModalActions = [];

    protected bool | Closure | null $isModalCentered = null;

    protected array | Closure | null $modalActions = null;

    protected ModalAction | Closure | null $modalCancelAction = null;

    protected ModalAction | Closure | null $modalSubmitAction = null;

    protected string | Closure | null $modalButtonLabel = null;

    protected View | Htmlable | Closure | null $modalContent = null;

    protected string | Closure | null $modalHeading = null;

    protected string | Closure | null $modalSubheading = null;

    protected string | Closure | null $modalWidth = null;

    public function centerModal(bool | Closure | null $condition = true): static
    {
        $this->isModalCentered = $condition;

        return $this;
    }

    public function modalActions(array | Closure | null $actions = null): static
    {
        $this->modalActions = $actions;

        return $this;
    }

    public function extraModalActions(array | Closure $actions): static
    {
        $this->extraModalActions = $actions;

        return $this;
    }

    public function modalSubmitAction(ModalAction | Closure | null $action = null): static
    {
        $this->modalSubmitAction = $action;

        return $this;
    }

    public function modalCancelAction(ModalAction | Closure | null $action = null): static
    {
        $this->modalCancelAction = $action;

        return $this;
    }

    public function modalButton(string | Closure | null $label = null): static
    {
        $this->modalButtonLabel = $label;

        return $this;
    }

    public function modalContent(View | Htmlable | Closure | null $content = null): static
    {
        $this->modalContent = $content;

        return $this;
    }

    public function modalHeading(string | Closure | null $heading = null): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalSubheading(string | Closure | null $subheading = null): static
    {
        $this->modalSubheading = $subheading;

        return $this;
    }

    public function modalWidth(string | Closure | null $width = null): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    abstract protected function getLivewireCallActionName(): string;

    public function getModalActions(): array
    {
        if ($this->isWizard()) {
            return [];
        }

        if ($this->modalActions !== null) {
            return $this->evaluate($this->modalActions);
        }

        $actions = array_merge(
            [$this->getModalSubmitAction()],
            $this->getExtraModalActions(),
            [$this->getModalCancelAction()],
        );

        if ($this->isModalCentered()) {
            $actions = array_reverse($actions);
        }

        return $actions;
    }

    public function getModalSubmitAction(): ModalAction
    {
        if ($this->modalSubmitAction) {
            return $this->evaluate($this->modalSubmitAction);
        }

        return static::makeModalAction('submit')
            ->label($this->getModalButtonLabel())
            ->submit($this->getLivewireCallActionName())
            ->color(match ($color = $this->getColor()) {
                'secondary' => 'primary',
                default => $color,
            });
    }

    public function getModalCancelAction(): ModalAction
    {
        if ($this->modalCancelAction) {
            return $this->evaluate($this->modalCancelAction);
        }

        return static::makeModalAction('cancel')
            ->label(__('filament-support::actions/modal.actions.cancel.label'))
            ->cancel()
            ->color('secondary');
    }

    public function getExtraModalActions(): array
    {
        return $this->evaluate($this->extraModalActions);
    }

    public function getModalButtonLabel(): string
    {
        if (filled($this->modalButtonLabel)) {
            return $this->evaluate($this->modalButtonLabel);
        }

        if ($this->isConfirmationRequired()) {
            return __('filament-support::actions/modal.actions.confirm.label');
        }

        return __('filament-support::actions/modal.actions.submit.label');
    }

    public function getModalContent(): View | Htmlable | null
    {
        return $this->evaluate($this->modalContent);
    }

    public function getModalHeading(): string
    {
        return $this->evaluate($this->modalHeading) ?? $this->getLabel();
    }

    public function getModalSubheading(): ?string
    {
        if (filled($this->modalSubheading)) {
            return $this->evaluate($this->modalSubheading);
        }

        if ($this->isConfirmationRequired()) {
            return __('filament-support::actions/modal.confirmation');
        }

        return null;
    }

    public function getModalWidth(): string
    {
        if (filled($this->modalWidth)) {
            return $this->evaluate($this->modalWidth);
        }

        if ($this->isConfirmationRequired()) {
            return 'sm';
        }

        return '4xl';
    }

    public function isModalCentered(): bool
    {
        if ($this->isModalCentered !== null) {
            return $this->evaluate($this->isModalCentered);
        }

        if (in_array($this->getModalWidth(), ['xs', 'sm'])) {
            return true;
        }

        return $this->isConfirmationRequired();
    }

    public function shouldOpenModal(): bool
    {
        return $this->isConfirmationRequired() || $this->hasFormSchema() || $this->getModalContent();
    }

    protected function makeExtraModalAction(string $name, ?array $arguments = null): ModalAction
    {
        return static::makeModalAction($name)
            ->action($this->getLivewireCallActionName(), $arguments)
            ->color('secondary');
    }

    protected static function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    public static function makeModalAction(string $name): ModalAction
    {
        return static::getModalActionClass()::make($name);
    }
}
