<?php

namespace Filament\Actions;

use Closure;
use Exception;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Livewire\Component;

class ActionGroup extends ViewComponent implements Arrayable, HasEmbeddedView
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

    public const BADGE_VIEW = 'filament::components.badge';

    public const BUTTON_VIEW = 'filament::components.button.index';

    public const GROUPED_VIEW = 'filament::components.dropdown.list.item';

    public const ICON_BUTTON_VIEW = 'filament::components.icon-button';

    public const LINK_VIEW = 'filament::components.link';

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
     * @var view-string
     */
    protected string $triggerView;

    /**
     * @var view-string | Closure | null
     */
    protected string | Closure | null $defaultTriggerView = null;

    protected View $triggerViewInstance;

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
        return $this->getTriggerView() === static::BADGE_VIEW;
    }

    public function button(): static
    {
        return $this->triggerView(static::BUTTON_VIEW);
    }

    public function isButton(): bool
    {
        return $this->getTriggerView() === static::BUTTON_VIEW;
    }

    public function grouped(): static
    {
        return $this->triggerView(static::GROUPED_VIEW);
    }

    public function iconButton(): static
    {
        return $this->triggerView(static::ICON_BUTTON_VIEW);
    }

    public function isIconButton(): bool
    {
        return $this->getTriggerView() === static::ICON_BUTTON_VIEW;
    }

    public function link(): static
    {
        return $this->triggerView(static::LINK_VIEW);
    }

    public function isLink(): bool
    {
        return $this->getTriggerView() === static::LINK_VIEW;
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
            'triggerView' => $this->getTriggerView(),
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
                fn (array $action): Action | ActionGroup => match (array_key_exists('actions', $action)) {
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

        $triggerView = $data['triggerView'] ?? null;

        if (filled($triggerView) && ($static->getTriggerView() !== $triggerView) && static::isViewSafe($triggerView)) {
            $static->triggerView($triggerView);
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
        return Str::startsWith($view, 'filament::');
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

    public function toEmbeddedHtml(): string
    {
        if (! $this->hasDropdown()) {
            return collect($this->getActions())
                ->filter(fn (Action | ActionGroup $action): bool => $action->isVisible())
                ->map(fn (Action | ActionGroup $action): string => $action->toHtml())
                ->implode('');
        }

        $actionLists = [];
        $singleActions = [];

        foreach ($this->getActions() as $action) {
            if ($action->isHidden()) {
                continue;
            }

            if ($action instanceof ActionGroup && (! $action->hasDropdown())) {
                if (count($singleActions)) {
                    $actionLists[] = $singleActions;
                    $singleActions = [];
                }

                $actionLists[] = array_filter(
                    $action->getActions(),
                    fn ($action): bool => $action->isVisible(),
                );
            } else {
                $singleActions[] = $action;
            }
        }

        if (count($singleActions)) {
            $actionLists[] = $singleActions;
        }

        $maxHeight = $this->getDropdownMaxHeight();
        $width = $this->getDropdownWidth();

        $panelAttributes = (new ComponentAttributeBag)
            ->class([
                'fi-dropdown-panel',
                ($width instanceof MaxWidth) ? "fi-width-{$width->value}" : (is_string($width) ? $width : 'fi-width-default'),
                'fi-scrollable' => $maxHeight,
            ])
            ->style([
                "max-height: {$maxHeight}" => $maxHeight,
            ]);

        ob_start(); ?>

        <div x-data="filamentDropdown" class="fi-dropdown">
            <div
                x-on:click="toggle"
                class="fi-dropdown-trigger"
            >
                <?= $this->renderTrigger()->render() ?>
            </div>

            <div
                x-cloak
                x-float.placement.<?= $this->getDropdownPlacement() ?? 'bottom-start' ?>.teleport.offset="{ offset: <?= $this->getDropdownOffset() ?? 8 ?> }"
                x-ref="panel"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                <?= $panelAttributes->toHtml() ?>
            >
                <?php foreach ($actionLists as $actions) { ?>
                    <div class="fi-dropdown-list">
                        <?php foreach ($actions as $action) { ?>
                            <?= $action->toHtml() ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getBadgeTriggerViewData(): array
    {
        return [
            'class' => 'fi-ac-badge-group',
            'iconPosition' => $this->getIconPosition(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getButtonTriggerViewData(): array
    {
        return [
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-btn-group',
            'iconPosition' => $this->getIconPosition(),
            'labeledFrom' => $this->getLabeledFromBreakpoint(),
            'outlined' => $this->isOutlined(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getGroupedTriggerViewData(): array
    {
        return [
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-grouped-group',
            'icon' => $this->getGroupedIcon(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getIconButtonTriggerViewData(): array
    {
        return [
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-icon-btn-group',
            'label' => $this->getLabel(),
            'size' => $this->getSize(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getLinkTriggerViewData(): array
    {
        return [
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-link-group',
            'iconPosition' => $this->getIconPosition(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
            'tag' => 'button',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getTriggerViewData(): array
    {
        return [
            'attributes' => $this->getExtraAttributeBag(),
            'color' => $this->getColor(),
            'icon' => $this->getIcon(),
            'iconSize' => $this->getIconSize(),
            'labelSrOnly' => $this->isLabelHidden(),
            'tooltip' => $this->getTooltip(),
            ...match ($this->getTriggerView()) {
                static::BADGE_VIEW => $this->getBadgeTriggerViewData(),
                static::BUTTON_VIEW => $this->getButtonTriggerViewData(),
                static::GROUPED_VIEW => $this->getGroupedTriggerViewData(),
                static::ICON_BUTTON_VIEW => $this->getIconButtonTriggerViewData(),
                static::LINK_VIEW => $this->getLinkTriggerViewData(),
                default => [],
            },
        ];
    }

    /**
     * @param  view-string | null  $view
     */
    public function triggerView(?string $view): static
    {
        if ($view === null) {
            return $this;
        }

        $this->triggerView = $view;

        return $this;
    }

    /**
     * @param  view-string | Closure | null  $view
     */
    public function defaultTriggerView(string | Closure | null $view): static
    {
        $this->defaultTriggerView = $view;

        return $this;
    }

    /**
     * @return view-string
     */
    public function getTriggerView(): string
    {
        if (isset($this->triggerView)) {
            return $this->triggerView;
        }

        if (filled($defaultView = $this->getDefaultTriggerView())) {
            return $defaultView;
        }

        throw new Exception('Class [' . static::class . '] extends [' . ActionGroup::class . '] but does not have a [$triggerView] property defined.');
    }

    /**
     * @return view-string | null
     */
    public function getDefaultTriggerView(): ?string
    {
        return $this->evaluate($this->defaultTriggerView);
    }

    public function renderTrigger(): View
    {
        return $this->triggerViewInstance ??= view(
            $this->getTriggerView(),
            $this->getTriggerViewData(),
        );
    }
}
