<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanBeHidden;
use Filament\Actions\Concerns\HasColor;
use Filament\Actions\Concerns\HasDropdown;
use Filament\Actions\Concerns\HasIcon;
use Filament\Actions\Concerns\HasLabel;
use Filament\Actions\Concerns\HasSize;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\Contracts\Groupable;
use Filament\Support\Components\ViewComponent;

class ActionGroup extends ViewComponent
{
    use CanBeHidden {
        isHidden as baseIsHidden;
    }
    use HasColor;
    use HasDropdown;
    use HasIcon;
    use HasLabel;
    use HasSize;
    use HasTooltip;

    protected string $view = 'filament-actions::group';

    protected string $evaluationIdentifier = 'group';

    protected string $viewIdentifier = 'group';

    /**
     * @param  array<Groupable&StaticAction>  $actions
     */
    public function __construct(
        protected array $actions,
    ) {
    }

    /**
     * @param  array<Groupable&StaticAction>  $actions
     */
    public static function make(array $actions): static
    {
        return app(static::class, ['actions' => $actions]);
    }

    public function getLabel(): ?string
    {
        $label = $this->evaluate($this->label);

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    /**
     * @return array<string, Groupable&StaticAction>
     */
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
