<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanBeInline;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Livewire\Component;

abstract class MountableAction extends StaticAction
{
    use Concerns\CanBeDisabled;
    use CanBeInline;
    use Concerns\CanBeMounted;
    use Concerns\CanBeOutlined;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanOpenUrl;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasForm;
    use Concerns\HasGroupedIcon;
    use Concerns\HasInfolist;
    use Concerns\HasKeyBindings;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasTooltip;
    use Concerns\HasWizard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::BUTTON_VIEW);

        $this->failureNotification(fn (Notification $notification): Notification => $notification);
        $this->successNotification(fn (Notification $notification): Notification => $notification);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(array $parameters = []): mixed
    {
        return $this->evaluate($this->getAction(), $parameters);
    }

    public function cancel(): void
    {
        throw new Cancel();
    }

    public function halt(): void
    {
        throw new Halt();
    }

    /**
     * @deprecated Use `->halt()` instead.
     */
    public function hold(): void
    {
        $this->halt();
    }

    public function success(): void
    {
        $this->sendSuccessNotification();
        $this->dispatchSuccessRedirect();
    }

    public function failure(): void
    {
        $this->sendFailureNotification();
        $this->dispatchFailureRedirect();
    }

    /**
     * @return Component
     */
    abstract public function getLivewire();

    public function getLivewireMountAction(): ?string
    {
        return null;
    }

    public function getAlpineMountAction(): ?string
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'arguments' => $this->getArguments(),
            'data' => $this->getFormData(),
            'livewire' => $this->getLivewire(),
        ]);
    }
}
