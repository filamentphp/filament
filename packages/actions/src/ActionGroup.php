<?php

namespace Filament\Actions;

use BackedEnum;
use Closure;
use Exception;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasBadgeTooltip;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateButtonHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownItemHtml;
use Filament\Support\View\Concerns\CanGenerateIconButtonHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class ActionGroup extends ViewComponent implements Arrayable, HasEmbeddedView
{
    use CanGenerateBadgeHtml;
    use CanGenerateButtonHtml;
    use CanGenerateDropdownItemHtml;
    use CanGenerateIconButtonHtml;
    use CanGenerateLinkHtml;
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToSchemaComponent;
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
    use HasBadgeTooltip;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon {
        HasIcon::getIcon as getBaseIcon;
    }
    use HasIconPosition;
    use HasIconSize;
    use HasTooltip;
    use InteractsWithRecord;

    public const BADGE_VIEW = 'filament::components.badge';

    public const BUTTON_VIEW = 'filament::components.button.index';

    public const BUTTON_GROUP_VIEW = 'filament::components.button.group';

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

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraDropdownAttributes = [];

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

        $this->defaultTriggerView(static::ICON_BUTTON_VIEW);
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

    public function badge(string | int | float | Closure | null $badge = null): static
    {
        if (func_num_args() === 0) {
            /** @phpstan-ignore-next-line */
            return $this->triggerView(static::BADGE_VIEW);
        }

        $this->badge = $badge;

        return $this;
    }

    public function button(): static
    {
        return $this->triggerView(static::BUTTON_VIEW);
    }

    public function buttonGroup(): static
    {
        return $this->triggerView(static::BUTTON_GROUP_VIEW);
    }

    public function isButton(): bool
    {
        return $this->getTriggerView() === static::BUTTON_VIEW;
    }

    public function isButtonGroup(): bool
    {
        return $this->getTriggerView() === static::BUTTON_GROUP_VIEW;
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
            fn (Action | ActionGroup $action) => match (true) {
                $action instanceof Action => $action->defaultView($this->isButtonGroup() ? $action::BUTTON_VIEW : $action::GROUPED_VIEW),
                $action instanceof ActionGroup => $action->defaultTriggerView($this->isButtonGroup() ? $action::BUTTON_VIEW : $action::GROUPED_VIEW),
            },
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

    public function hasNonBulkAction(): bool
    {
        foreach ($this->getFlatActions() as $action) {
            if (! $action->isBulk()) {
                return true;
            }
        }

        return false;
    }

    public function getIcon(): string | BackedEnum
    {
        return $this->getBaseIcon() ?? FilamentIcon::resolve(ActionsIconAlias::ACTION_GROUP) ?? Heroicon::EllipsisVertical;
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
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel() ?? $this->getSchemaContainer()?->getModel() ?? $this->getSchemaComponent()?->getModel()],
            'mountedActions' => [$this->getLivewire()->getMountedActions()],
            'record' => [$this->getRecord() ?? $this->getSchemaContainer()?->getRecord() ?? $this->getSchemaComponent()?->getRecord()],
            'schema' => [$this->getSchemaContainer()],
            'schemaComponent', 'component' => [$this->getSchemaComponent()],
            'schemaOperation', 'context', 'operation' => [$this->getSchemaContainer()?->getOperation() ?? $this->getSchemaComponent()?->getContainer()->getOperation()],
            'schemaGet', 'get' => [$this->getSchemaComponent()->makeGetUtility()],
            'schemaComponentState', 'state' => [$this->getSchemaComponent()->getState()],
            'table' => [$this->getTable()],
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
        if ($this->isButtonGroup()) {
            ob_start(); ?>

            <div class="fi-btn-group">
                <?php foreach ($this->getActions() as $action) { ?>
                    <?php if ($action->isVisible()) { ?>
                        <?= $action->toHtml() ?>
                    <?php } ?>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

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
                ($width instanceof Width) ? "fi-width-{$width->value}" : (is_string($width) ? $width : ''),
                'fi-scrollable' => $maxHeight,
            ])
            ->style([
                "max-height: {$maxHeight}" => $maxHeight,
            ]);

        ob_start(); ?>

        <div
            x-data="filamentDropdown"
            <?= $this->getExtraDropdownAttributeBag()->class(['fi-dropdown'])->toHtml() ?>
        >
            <div
                x-on:mousedown="toggle"
                class="fi-dropdown-trigger"
            >
                <?= $this->toTriggerHtml() ?>
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

    public function toTriggerHtml(): string
    {
        return match ($this->getTriggerView()) {
            static::BADGE_VIEW => $this->toBadgeTriggerHtml(),
            static::BUTTON_VIEW => $this->toButtonTriggerHtml(),
            static::GROUPED_VIEW => $this->toGroupedTriggerHtml(),
            static::ICON_BUTTON_VIEW => $this->toIconButtonTriggerHtml(),
            static::LINK_VIEW => $this->toLinkTriggerHtml(),
            default => $this->renderTrigger()->render(),
        };
    }

    protected function toBadgeTriggerHtml(): string
    {
        return $this->generateBadgeHtml(
            attributes: (new ComponentAttributeBag)
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-badge-group']),
            color: $this->getColor(),
            icon: $this->getIcon(),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: 'button',
            tooltip: $this->getTooltip(),
        );
    }

    protected function toButtonTriggerHtml(): string
    {
        return $this->generateButtonHtml(
            attributes: (new ComponentAttributeBag)
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-btn-group']),
            badge: $this->getBadge(),
            badgeColor: $this->getBadgeColor(),
            color: $this->getColor(),
            icon: $this->getIcon(),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            isLabelSrOnly: $this->isLabelHidden(),
            isOutlined: $this->isOutlined(),
            label: $this->getLabel(),
            labeledFromBreakpoint: $this->getLabeledFromBreakpoint(),
            size: $this->getSize(),
            tag: 'button',
            tooltip: $this->getTooltip(),
        );
    }

    protected function toGroupedTriggerHtml(): string
    {
        return $this->generateDropdownItemHtml(
            attributes: (new ComponentAttributeBag)
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-grouped-group']),
            badge: $this->getBadge(),
            badgeColor: $this->getBadgeColor(),
            badgeTooltip: $this->getBadgeTooltip(),
            color: $this->getColor(),
            icon: $this->getIcon(),
            iconSize: $this->getIconSize(),
            label: $this->getLabel(),
            tag: 'button',
            tooltip: $this->getTooltip(),
        );
    }

    protected function toIconButtonTriggerHtml(): string
    {
        return $this->generateIconButtonHtml(
            attributes: (new ComponentAttributeBag)
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-icon-btn-group']),
            badge: $this->getBadge(),
            badgeColor: $this->getBadgeColor(),
            color: $this->getColor(),
            icon: $this->getIcon(),
            iconSize: $this->getIconSize(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: 'button',
            tooltip: $this->getTooltip(),
        );
    }

    protected function toLinkTriggerHtml(): string
    {
        return $this->generateLinkHtml(
            attributes: (new ComponentAttributeBag)
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-link-group']),
            badge: $this->getBadge(),
            badgeColor: $this->getBadgeColor(),
            color: $this->getColor(),
            icon: $this->getIcon(),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            isLabelSrOnly: $this->isLabelHidden(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: 'button',
            tooltip: $this->getTooltip(),
        );
    }

    public function renderTrigger(): View
    {
        return view(
            $this->getTriggerView(),
            [
                'attributes' => new ComponentAttributeBag,
                ...$this->extractPublicMethods(),
                ...(isset($this->viewIdentifier) ? [$this->viewIdentifier => $this] : []),
                ...$this->viewData,
            ],
        );
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->cloneActions();

        return $clone;
    }

    protected function cloneActions(): void
    {
        $this->actions = array_map(
            fn (Action | ActionGroup $action): Action | ActionGroup => $action->getClone()->group($this),
            $this->actions,
        );

        $this->flatActions = array_map(
            fn (Action $action): Action => $action->getClone()->group($this),
            $this->flatActions,
        );
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraDropdownAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraDropdownAttributes[] = $attributes;
        } else {
            $this->extraDropdownAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraDropdownAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraDropdownAttributes as $extraDropdownAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraDropdownAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraDropdownAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraDropdownAttributes());
    }
}
