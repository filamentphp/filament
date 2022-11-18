<?php

namespace Filament\Forms\Components\Actions;

use Filament\Actions\MountableAction;

class Action extends MountableAction
{
    use Concerns\BelongsToComponent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }

    public function getLivewireCallActionName(): string
    {
        return 'callMountedFormComponentAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if ($this->getUrl()) {
            return null;
        }

        return "mountFormComponentAction('{$this->getComponent()->getStatePath()}', '{$this->getName()}')";
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'component' => $this->getComponent(),
        ]);
    }
}
