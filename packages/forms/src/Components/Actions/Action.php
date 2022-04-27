<?php

namespace Filament\Forms\Components\Actions;

use Filament\Forms\Components\Actions\Modal\Actions\Action as ModalAction;
use Filament\Support\Actions\Action as BaseAction;

class Action extends BaseAction
{
    use Concerns\BelongsToComponent;

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedFormComponentAction';
    }

    protected function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'component' => $this->getComponent(),
        ]);
    }
}
