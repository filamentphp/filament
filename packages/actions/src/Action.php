<?php

namespace Filament\Actions;

use Filament\Actions\Contracts\Groupable;
use Filament\Actions\Contracts\HasRecord;
use Illuminate\Database\Eloquent\Model;

class Action extends MountableAction implements Groupable, HasRecord
{
    use Concerns\BelongsToLivewire;
    use Concerns\InteractsWithRecord;

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
