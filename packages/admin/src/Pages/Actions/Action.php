<?php

namespace Filament\Pages\Actions;

use Filament\Pages\Actions\Modal\Actions\Action as ModalAction;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\CanSubmitForm;
use Filament\Support\Actions\Concerns\HasGroupedIcon;
use Filament\Support\Actions\Concerns\HasKeyBindings;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Actions\Contracts\Groupable;
use Filament\Support\Actions\Contracts\HasRecord;
use Illuminate\Database\Eloquent\Model;

class Action extends BaseAction implements Groupable, HasRecord
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use CanSubmitForm;
    use Concerns\BelongsToLivewire;
    use HasGroupedIcon;
    use HasKeyBindings;
    use HasTooltip;
    use InteractsWithRecord;

    protected string $view = 'filament::pages.actions.button-action';

    public function button(): static
    {
        $this->view('filament::pages.actions.button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('filament::pages.actions.grouped-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament::pages.actions.icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament::pages.actions.link-action');

        return $this;
    }

    protected function getLivewireCallActionName(): string
    {
        return 'callMountedAction';
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
}
