<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;

class Action extends BaseAction
{
    use CanBeDisabled;
    use CanOpenUrl;
    use Concerns\BelongsToTable;
    use Concerns\HasRecord;
    use HasTooltip;

    public function button(): static
    {
        $this->view('tables::actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('tables::actions.link-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('tables::actions.icon-button-action');

        return $this;
    }

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        return $this->evaluate($this->getAction(), [
            'data' => $data,
        ]);
    }

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedTableAction';
    }

    protected function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'record' => $this->getRecord(),
        ]);
    }
}
