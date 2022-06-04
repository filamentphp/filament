<?php

namespace Filament\Support\Actions;

abstract class Action extends BaseAction
{
    use Concerns\CanBeMounted;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasFormSchema;
    use Concerns\HasWizard;

    public function call()
    {
        return $this->evaluate($this->getAction());
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
