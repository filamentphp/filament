<?php

namespace Filament\Support\Actions;

use Filament\Support\Actions\Concerns\CanBeHidden;
use Filament\Support\Actions\Concerns\HasColor;
use Filament\Support\Actions\Concerns\HasIcon;
use Filament\Support\Actions\Concerns\HasLabel;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Components\ViewComponent;

class ActionGroup extends ViewComponent
{
    use CanBeHidden {
        isHidden as baseIsHidden;
    }
    use HasIcon;
    use HasTooltip;
    use HasLabel;
    use HasColor;

    protected string $evaluationIdentifier = 'group';

    protected string $viewIdentifier = 'group';

    public function __construct(
        protected array $actions,
    ) {
    }

    public static function make(array $actions): static
    {
        return app(static::class, ['actions' => $actions]);
    }

    public function getLabel(): ?string
    {
        $label = $this->evaluate($this->label);

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    public function getActions(): array
    {
        $actions = [];

        foreach ($this->actions as $action) {
            $actions[$action->getName()] = $action->grouped();
        }

        return $actions;
    }

    public function isHidden(): bool
    {
        $condition = $this->baseIsHidden();

        if ($condition) {
            return true;
        }

        foreach ($this->getActions() as $action) {
            if ($action->isHidden()) {
                continue;
            }

            return false;
        }

        return true;
    }
}
