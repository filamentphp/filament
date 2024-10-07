<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class ActionGroup extends ViewComponent implements Arrayable
{
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden {
        isHidden as baseIsHidden;
    }
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeOutlined;
    use Concerns\HasDropdown;
    use Concerns\HasGroupedIcon;
    use Concerns\HasLabel;
    use Concerns\HasSize;
    use HasBadge;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon {
        HasIcon::getIcon as getBaseIcon;
    }
    use HasTooltip;
    use InteractsWithRecord;

    public const BADGE_VIEW = 'filament-actions::badge-group';

    public const BUTTON_VIEW = 'filament-actions::button-group';

    public const GROUPED_VIEW = 'filament-actions::grouped-group';

    public const ICON_BUTTON_VIEW = 'filament-actions::icon-button-group';

    public const LINK_VIEW = 'filament-actions::link-group';

    /**
     * @var array<Action | ActionGroup>
     */
    protected array $actions;

    /**
     * @var array<string, Action>
     */
    protected array $flatActions;

    protected Component $livewire;

    protected string $evaluationIdentifier = 'group';

    protected string $viewIdentifier = 'group';

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    public function __construct(array $actions)
    {
        $this->actions($actions);
    }

    /**
     * @param  array<Action | ActionGroup>  $actions
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
     * @param  array<Action | ActionGroup>  $actions
     */
    public function actions(array $actions): static
    {
        $this->actions = [];
        $this->flatActions = [];

        foreach ($actions as $action) {
            $action->group($this);

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
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): object
    {
        if (isset($this->livewire)) {
            return $this->livewire;
        }

        return $this->getGroup()?->getLivewire();
    }

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label) ?? __('filament-actions::group.trigger.label');

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getActions(): array
    {
        return array_map(
            fn (Action | ActionGroup $action) => $action->defaultView($action::GROUPED_VIEW),
            $this->actions,
        );
    }

    /**
     * @return array<string, Action>
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
        if ($this->baseIsHidden()) {
            return true;
        }

        foreach ($this->getActions() as $action) {
            if ($action->isHiddenInGroup()) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'actions' => collect($this->getActions())->toArray(),
            'color' => $this->getColor(),
            'dropdownMaxHeight' => $this->getDropdownMaxHeight(),
            'dropdownOffset' => $this->getDropdownOffset(),
            'dropdownPlacement' => $this->getDropdownPlacement(),
            'dropdownWidth' => $this->getDropdownWidth(),
            'extraAttributes' => $this->getExtraAttributes(),
            'hasDropdown' => $this->hasDropdown(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'iconSize' => $this->getIconSize(),
            'isOutlined' => $this->isOutlined(),
            'label' => $this->getLabel(),
            'size' => $this->getSize(),
            'tooltip' => $this->getTooltip(),
            'view' => $this->getView(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make(
            array_map(
                fn (array $action): Action | \Filament\Actions\ActionGroup => match (array_key_exists('actions', $action)) {
                    true => ActionGroup::fromArray($action),
                    false => Action::fromArray($action),
                },
                $data['actions'] ?? [],
            ),
        );

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
        }

        $static->color($data['color'] ?? null);
        $static->dropdown($data['hasDropdown'] ?? false);
        $static->dropdownMaxHeight($data['dropdownMaxHeight'] ?? null);
        $static->dropdownOffset($data['dropdownOffset'] ?? null);
        $static->dropdownPlacement($data['dropdownPlacement'] ?? null);
        $static->dropdownWidth($data['dropdownWidth'] ?? null);
        $static->extraAttributes($data['extraAttributes'] ?? []);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->outlined($data['isOutlined'] ?? null);
        $static->tooltip($data['tooltip'] ?? null);

        return $static;
    }

    /**
     * @param  view-string  $view
     */
    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament-actions::');
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        return match ($parameterType) {
            Model::class, ($record instanceof Model) ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
