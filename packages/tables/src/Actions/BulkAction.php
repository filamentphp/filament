<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\HasRecords;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;

class BulkAction extends BaseAction
{
    use Concerns\BelongsToTable;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use HasRecords;

    protected string $view = 'tables::actions.bulk-action';

    public function call(array $data = [])
    {
        try {
            return $this->evaluate($this->getAction());
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

    protected static function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    public static function makeModalAction(string $name): ModalAction
    {
        /** @var ModalAction $action */
        $action = parent::makeModalAction($name);

        return $action;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'records' => $this->getRecords(),
        ]);
    }
}
