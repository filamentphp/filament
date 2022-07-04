<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasGroupedIcon;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Actions\Contracts\Groupable;
use Filament\Support\Actions\Contracts\HasRecord;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Illuminate\Database\Eloquent\Model;

class Action extends BaseAction implements Groupable, HasRecord
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use Concerns\BelongsToTable;
    use HasGroupedIcon;
    use HasTooltip;
    use InteractsWithRecord;

    protected string $view = 'tables::actions.link-action';

    public function button(): static
    {
        $this->view('tables::actions.button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('tables::actions.grouped-action');

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

    protected function getLivewireCallActionName(): string
    {
        return 'callMountedTableAction';
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
            'record' => $this->resolveEvaluationParameter(
                'record',
                fn (): ?Model => $this->getRecord(),
            ),
        ]);
    }

    public function getRecordTitle(?Model $record = null): string
    {
        $record ??= $this->getRecord();

        return $this->getCustomRecordTitle($record) ?? $this->getLivewire()->getTableRecordTitle($record);
    }

    public function getModelLabel(): string
    {
        return $this->getCustomModelLabel() ?? $this->getLivewire()->getTableModelLabel();
    }

    public function getPluralModelLabel(): string
    {
        return $this->getCustomPluralModelLabel() ?? $this->getLivewire()->getTablePluralModelLabel();
    }

    public function getModel(): string
    {
        return $this->getCustomModel() ?? $this->getLivewire()->getTableModel();
    }
}
