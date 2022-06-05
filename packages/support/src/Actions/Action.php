<?php

namespace Filament\Support\Actions;

use Filament\Support\Actions\Exceptions\Hold;

abstract class Action extends BaseAction
{
    use Concerns\CanBeMounted;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasFormSchema;
    use Concerns\HasWizard;

    public function call()
    {
        return $this->evaluate($this->getAction());
    }

    public function hold(): void
    {
        throw new Hold();
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
            'data' => $this->getFormData(),
            'livewire' => $this->getLivewire(),
        ]);
    }
}
