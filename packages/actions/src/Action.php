<?php

namespace Filament\Actions;

use Filament\Actions\Contracts\Groupable;
use Filament\Actions\Contracts\HasRecord;
use Illuminate\Database\Eloquent\Model;

class Action extends MountableAction implements Groupable, HasRecord
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeOutlined;
    use Concerns\CanOpenUrl;
    use Concerns\CanSubmitForm;
    use Concerns\BelongsToLivewire;
    use Concerns\HasGroupedIcon;
    use Concerns\HasKeyBindings;
    use Concerns\HasTooltip;
    use Concerns\InteractsWithRecord;

    protected string $view = 'filament-actions::button-action';

    public function button(): static
    {
        $this->view('filament-actions::button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('filament-actions::grouped-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament-actions::icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament-actions::link-action');

        return $this;
    }

    public function getLivewireCallActionName(): string
    {
        return 'callMountedAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if ($this->getUrl()) {
            return null;
        }

        return "mountAction('{$this->getName()}')";
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
