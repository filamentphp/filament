<?php

namespace Filament\Support\Actions;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;

abstract class Action extends BaseAction
{
    use Concerns\CanBeMounted;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasForm;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasWizard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->failureNotification(fn (Notification $notification): Notification => $notification);
        $this->successNotification(fn (Notification $notification): Notification => $notification);
    }

    public function call(array $parameters = [])
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

    abstract public function getLivewire();

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'arguments' => $this->getArguments(),
            'data' => $this->getFormData(),
            'livewire' => $this->getLivewire(),
        ]);
    }
}
