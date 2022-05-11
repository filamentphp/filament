<?php

namespace Filament\Support\Actions;

use Filament\Support\Concerns\EvaluatesClosures;

abstract class Action extends BaseAction
{
    use Concerns\CanBeHidden;
    use Concerns\CanBeMounted;
    use Concerns\CanOpenModal;
    use Concerns\CanRequireConfirmation;
    use Concerns\HasAction;
    use Concerns\HasFormSchema;

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        return $this->evaluate($this->getAction(), [
            'data' => $data,
        ]);
    }

    abstract public function getLivewire();

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
        ]);
    }
}
