<?php

namespace Filament\Actions;

use Filament\Actions\Contracts\HasLivewire;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Livewire\Component;

class ActionGroup extends ViewComponent implements HasLivewire
{
    use Concerns\CanBeDivided;
    use Concerns\CanBeHidden {
        isHidden as baseIsHidden;
    }
    use Concerns\CanBeInline;
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeOutlined;
    use Concerns\HasColor;
    use Concerns\HasDropdown;
    use Concerns\HasGroupedIcon;
    use Concerns\HasIcon {
        getIcon as getBaseIcon;
    }
    use Concerns\HasIndicator;
    use Concerns\HasLabel;
    use Concerns\HasSize;
    use Concerns\HasTooltip;
    use HasExtraAttributes;

    /**
     * @var array<StaticAction | ActionGroup>
     */
    protected array $actions;

    /**
     * @var array<string, StaticAction>
     */
    protected array $flatActions;

    protected string $evaluationIdentifier = 'group';

    protected string $viewIdentifier = 'group';

    /**
     * @param  array<StaticAction | ActionGroup>  $actions
     */
    public function __construct(array $actions)
    {
        $this->actions($actions);
    }

    /**
     * @param  array<StaticAction | ActionGroup>  $actions
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

    /**
     * @param  array<StaticAction | ActionGroup>  $actions
     */
    public function actions(array $actions): static
    {
        $this->actions = [];
        $this->flatActions = [];

        foreach ($actions as $action) {
            $action->grouped();

            if ($action instanceof ActionGroup) {
                $action->dropdownPlacement('right-top');

                $this->flatActions = [
                    ...$this->flatActions,
                    ...$action->getFlatActions(),
                ];
            } else {
                $this->flatActions[$action->getName()] = $action;
            }

            $this->actions[] = $action;
        }

        return $this;
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

    public function livewire(Component $livewire): static
    {
        foreach ($this->actions as $action) {
            if (! $action instanceof HasLivewire) {
                continue;
            }

            $action->livewire($livewire);
        }

        return $this;
    }

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label) ?? __('filament-actions::group.trigger.label');

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    /**
     * @return array<StaticAction | ActionGroup>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return array<string, StaticAction>
     */
    public function getFlatActions(): array
    {
        return $this->flatActions;
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
