<?php

namespace Filament\Actions;

use Filament\Actions\Contracts\HasLivewire;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;

abstract class MountableAction extends StaticAction implements HasLivewire
{
    use Concerns\BelongsToLivewire;
    use Concerns\CanBeMounted;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasForm;
    use Concerns\HasInfolist;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasParentActions;
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
        return $this->evaluate($this->getActionFunction(), $parameters);
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
     * @deprecated Use `halt()` instead.
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
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'arguments' => [$this->getArguments()],
            'data' => [$this->getFormData()],
            'livewire' => [$this->getLivewire()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
