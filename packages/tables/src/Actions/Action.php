<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Actions\Contracts\HasRecord;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Illuminate\Database\Eloquent\Model;

class Action extends BaseAction implements HasRecord
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use Concerns\BelongsToTable;
    use HasTooltip;
    use InteractsWithRecord;

    protected string $view = 'tables::actions.link-action';

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

    public function getModelLabel(): ?string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        return $this->getLivewire()->getTableModelLabel();
    }

    public function getPluralModelLabel(): ?string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        return $this->getLivewire()->getTablePluralModelLabel();
    }

    public function getRecordTitleAttribute(?Model $record = null): ?string
    {
        $attribute = $this->evaluate($this->recordTitleAttribute, [
            'record' => $record ?? $this->getRecord(),
        ]);

        if (filled($attribute)) {
            return $attribute;
        }

        return $this->getLivewire()->getTableRecordTitleAttribute();
    }
}
