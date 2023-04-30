<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanBeHidden;
use Filament\Actions\Concerns\CanBeInline;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasColor;
use Filament\Actions\Concerns\HasDropdown;
use Filament\Actions\Concerns\HasGroupedIcon;
use Filament\Actions\Concerns\HasIcon;
use Filament\Actions\Concerns\HasLabel;
use Filament\Actions\Concerns\HasSize;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\Contracts\Groupable;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;

class ActionGroup extends ViewComponent
{
    use CanBeHidden {
        isHidden as baseIsHidden;
    }
    use CanBeInline;
    use CanBeOutlined;
    use HasColor;
    use HasDropdown;
    use HasExtraAttributes;
    use HasGroupedIcon;
    use HasIcon {
        getIcon as getBaseIcon;
    }
    use Concerns\HasIndicator;
    use HasLabel;
    use HasSize;
    use HasTooltip;

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
        $static = app(static::class, ['actions' => $actions]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }

    public function button(): static
    {
        $this->view('filament-actions::button-group');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('filament-actions::grouped-group');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament-actions::icon-button-group');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament-actions::link-group');

        return $this;
    }

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label) ?? __('filament-actions::group.trigger.label');

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

    public function getIcon(): string
    {
        return $this->getBaseIcon() ?? 'heroicon-m-ellipsis-vertical';
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
