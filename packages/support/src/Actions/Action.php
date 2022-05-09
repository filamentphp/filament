<?php

namespace Filament\Support\Actions;

abstract class Action extends BaseAction
{
    use Concerns\CanBeHidden;
    use Concerns\CanBeMounted;
    use Concerns\CanOpenModal;
    use Concerns\CanRequireConfirmation;
    use Concerns\EvaluatesClosures;
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
}
