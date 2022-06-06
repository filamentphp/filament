<?php

namespace Filament\Forms\Components\Actions;

use Filament\Forms\Components\Actions\Modal\Actions\Action as ModalAction;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\HasTooltip;

class Action extends BaseAction
{
    use Concerns\BelongsToComponent;
    use CanBeDisabled;
    use CanOpenUrl;
    use HasTooltip;

    protected string $view = 'forms::components.actions.icon-button-action';

    public function iconButton(): static
    {
        $this->view('forms::components.actions.icon-button-action');

        return $this;
    }

    protected function getLivewireCallActionName(): string
    {
        return 'callMountedFormComponentAction';
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
            'component' => $this->getComponent(),
        ]);
    }
}
