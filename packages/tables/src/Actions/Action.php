<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;

class Action extends BaseAction
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use Concerns\BelongsToTable;
    use Concerns\HasDefaultIcon;
    use Concerns\HasRecord;
    use HasTooltip;

    protected function setUp(): void
    {
        $this->view ?? $this->link();

        parent::setUp();
    }

    public function button(): static
    {
        $this->view('tables::actions.button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view($this->getLinkView());

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('tables::actions.icon-button-action');

        return $this;
    }

    public function getIcon(): ?string
    {
        return parent::getIcon() ?? $this->getDefaultIcon();
    }

    protected function getLinkView(): string
    {
        return 'tables::actions.link-action';
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
            'record' => $this->getRecord(),
        ]);
    }
}
