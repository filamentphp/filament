<?php

namespace Filament\Actions;

use Filament\Actions\Contracts\HasLivewire;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Facades\FilamentIcon;
use Livewire\Component;

class ActionGroup extends ViewComponent implements HasLivewire
{
    use Concerns\CanBeHidden {
        isHidden as baseIsHidden;
    }
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeOutlined;
    use Concerns\HasDropdown;
    use Concerns\HasGroupedIcon;
    use Concerns\HasLabel;
    use Concerns\HasSize;
    use Concerns\HasTooltip;
    use HasBadge;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon {
        getIcon as getBaseIcon;
    }

    public const BADGE_VIEW = 'filament-actions::badge-group';

    public const BUTTON_VIEW = 'filament-actions::button-group';

    public const GROUPED_VIEW = 'filament-actions::grouped-group';

    public const ICON_BUTTON_VIEW = 'filament-actions::icon-button-group';

    public const LINK_VIEW = 'filament-actions::link-group';

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

    public function isBadge(): bool
    {
        return $this->getView() === static::BADGE_VIEW;
    }

    public function button(): static
    {
        return $this->view(static::BUTTON_VIEW);
    }

    public function isButton(): bool
    {
        return $this->getView() === static::BUTTON_VIEW;
    }

    public function grouped(): static
    {
        return $this->view(static::GROUPED_VIEW);
    }

    public function iconButton(): static
    {
        return $this->view(static::ICON_BUTTON_VIEW);
    }

    public function isIconButton(): bool
    {
        return $this->getView() === static::ICON_BUTTON_VIEW;
    }

    public function link(): static
    {
        return $this->view(static::LINK_VIEW);
    }

    public function isLink(): bool
    {
        return $this->getView() === static::LINK_VIEW;
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
        return array_map(
            fn (StaticAction | ActionGroup $action) => $action->defaultView($action::GROUPED_VIEW),
            $this->actions,
        );
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
        return $this->getBaseIcon() ?? FilamentIcon::resolve('actions::action-group') ?? 'heroicon-m-ellipsis-vertical';
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
