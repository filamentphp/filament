<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;

class BulkAction extends BaseAction
{
    use Concerns\BelongsToTable;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\HasRecords;

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        $action = $this->getAction();

        if (! $action) {
            return;
        }

        try {
            return $this->evaluate($action, [
                'data' => $data,
            ]);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }

    public function getAction(): ?Closure
    {
        $action = $this->action;

        if (is_string($action)) {
            $action = Closure::fromCallable([$this->getLivewire(), $action]);
        }

        return $action;
    }

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedTableBulkAction';
    }

    protected function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'records' => $this->getRecords(),
        ]);
    }
}
