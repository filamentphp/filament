<?php

namespace Filament\Forms\Components\Actions;

use Filament\Actions\Concerns\HasMountableArguments;
use Filament\Actions\MountableAction;
use Illuminate\Support\Js;

class Action extends MountableAction
{
    use Concerns\BelongsToComponent;
    use HasMountableArguments;

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
        if (! $this->isMountedOnClick()) {
            return null;
        }

        if ($this->getUrl()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
            $argumentsParameter .= '';
        }

        return "mountFormComponentAction('{$this->getComponent()->getStatePath()}', '{$this->getName()}'{$argumentsParameter})";
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'component' => $this->getComponent(),
        ]);
    }
}
